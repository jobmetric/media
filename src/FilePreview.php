<?php

namespace JobMetric\Media;

use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use JobMetric\Media\Models\Media;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use ZipArchive;

class FilePreview
{
    /** @return array<string, mixed> */
    public function preview(Media $item, array $options = []): array
    {
        $disk = Storage::disk($item->disk);
        $key = app(FileManager::class)->path($item);
        if ($item->type !== 'f' || !$disk->exists($key) || $item->size > 64 * 1024 * 1024) {
            throw ValidationException::withMessages(['file' => trans('media::file-manager.preview_unavailable')]);
        }
        $temporary = null;
        if (config('filesystems.disks.'.$item->disk.'.driver') === 'local') $path = $disk->path($key);
        else {
            $path = $temporary = tempnam(sys_get_temp_dir(), 'media-preview-');
            $input = $disk->readStream($key); $output = fopen($path, 'wb');
            try { stream_copy_to_stream($input, $output, 64 * 1024 * 1024); }
            finally { if(is_resource($input)) fclose($input); if(is_resource($output)) fclose($output); }
        }
        $extension = mb_strtolower((string) ($item->extension ?: pathinfo($item->name, PATHINFO_EXTENSION)));
        try {
            if ($reader = app(FilePreviewRegistry::class)->get($extension)) return $reader($path, $item, $options);
            return match ($extension) {
            'docx', 'pptx' => $this->office($path, $extension),
            'xls', 'xlsx', 'csv' => $this->spreadsheet($path, $item, $options),
            'zip' => $this->archive($path, $item, $options),
            default => [
                'kind' => 'direct',
                'name' => $item->name,
                'mime_type' => $item->mime_type,
                'url' => route('media.download', $item->id),
            ],
        }; } finally { if ($temporary && is_file($temporary)) unlink($temporary); }
    }

    /** Return bounded, non-executable OOXML document paragraphs or slide text. */
    private function office(string $path, string $extension): array
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw ValidationException::withMessages(['file' => trans('media::file-manager.preview_unavailable')]);
        }
        try {
            $names = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (($extension === 'docx' && $name === 'word/document.xml') || ($extension === 'pptx' && preg_match('~^ppt/slides/slide\d+\.xml$~', $name))) $names[] = $name;
            }
            natsort($names);
            if (count($names) > 300) throw ValidationException::withMessages(['file' => trans('media::file-manager.preview_unavailable')]);
            $pages = []; $bytes = 0;
            foreach ($names as $name) {
                $stat = $zip->statName($name); $bytes += $stat['size'];
                if ($bytes > 16 * 1024 * 1024) throw ValidationException::withMessages(['file' => trans('media::file-manager.preview_unavailable')]);
                $xml = $zip->getFromName($name);
                if (stripos($xml, '<!DOCTYPE') !== false || stripos($xml, '<!ENTITY') !== false) throw ValidationException::withMessages(['file' => trans('media::file-manager.preview_unavailable')]);
                $document = new \DOMDocument;
                $previous = libxml_use_internal_errors(true);
                try { $loaded = $document->loadXML($xml, LIBXML_NONET); }
                finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
                if (!$loaded) throw ValidationException::withMessages(['file' => trans('media::file-manager.preview_unavailable')]);
                $xpath = new \DOMXPath($document); $paragraphs = [];
                foreach ($xpath->query('//*[local-name()="p"]') as $paragraph) {
                    $text = '';
                    foreach ($xpath->query('.//*[local-name()="t"]', $paragraph) as $run) $text .= $run->textContent;
                    if (trim($text) !== '') $paragraphs[] = $text;
                }
                $pages[] = ['paragraphs' => $paragraphs];
            }
            return ['kind' => $extension === 'pptx' ? 'presentation' : 'document', 'pages' => $pages];
        } finally { $zip->close(); }
    }

    /** @return array<string, mixed> */
    private function spreadsheet(string $path, Media $item, array $options): array
    {
        try {
            $reader = IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(false);
            $sheetNames = array_values($reader->listWorksheetNames($path));
            $sheetIndex = max(0, min((int) ($options['sheet'] ?? 0), max(0, count($sheetNames) - 1)));
            $sheetName = $sheetNames[$sheetIndex] ?? null;

            if ($sheetName === null) {
                throw new \RuntimeException('Spreadsheet has no worksheet.');
            }

            $reader->setLoadSheetsOnly($sheetName);
            $spreadsheet = $reader->load($path);
            $worksheet = $spreadsheet->getSheetByName($sheetName) ?: $spreadsheet->getActiveSheet();
            $highestRow = max(1, $worksheet->getHighestDataRow());
            $highestColumn = max(1, Coordinate::columnIndexFromString($worksheet->getHighestDataColumn()));
            $maxColumns = max(10, (int) config('file-manager.preview.spreadsheet.max_columns', 150));
            $columnCount = min($highestColumn, $maxColumns);
            $offset = max(0, min((int) ($options['offset'] ?? 0), $highestRow - 1));
            $limit = max(25, min((int) ($options['limit'] ?? 150), 250));
            $endRow = min($highestRow, $offset + $limit);
            $rows = [];

            for ($rowNumber = $offset + 1; $rowNumber <= $endRow; $rowNumber++) {
                $cells = [];
                for ($column = 1; $column <= $columnCount; $column++) {
                    $coordinate = Coordinate::stringFromColumnIndex($column).$rowNumber;
                    $cell = $worksheet->getCell($coordinate);
                    $value = (string) $cell->getFormattedValue();
                    $style = $worksheet->getStyle($coordinate);
                    $font = $style->getFont();
                    $fill = $style->getFill();
                    $alignment = $style->getAlignment();
                    $fontColor = $this->rgb($font->getColor()->getARGB());
                    $fillColor = $fill->getFillType() === Fill::FILL_NONE ? null : $this->rgb($fill->getStartColor()->getARGB());
                    $hyperlink = $cell->getHyperlink()->getUrl();

                    $cells[] = [
                        'value' => $value,
                        'style' => array_filter([
                            'bold' => $font->getBold() ?: null,
                            'italic' => $font->getItalic() ?: null,
                            'color' => $fontColor !== '#000000' ? $fontColor : null,
                            'background' => $fillColor,
                            'align' => in_array($alignment->getHorizontal(), ['left', 'center', 'right'], true) ? $alignment->getHorizontal() : null,
                            'wrap' => $alignment->getWrapText() ?: null,
                        ], fn ($value) => $value !== null && $value !== false),
                        'link' => preg_match('#^https?://#i', $hyperlink) ? $hyperlink : null,
                    ];
                }
                $rows[] = ['number' => $rowNumber, 'cells' => $cells];
            }

            $columns = [];
            for ($column = 1; $column <= $columnCount; $column++) {
                $letter = Coordinate::stringFromColumnIndex($column);
                $width = (float) $worksheet->getColumnDimension($letter)->getWidth();
                $columns[] = ['letter' => $letter, 'width' => $width > 0 ? min(50, $width) : 12];
            }

            $result = [
                'kind' => 'spreadsheet',
                'name' => $item->name,
                'sheets' => array_map(fn (string $name, int $index) => ['index' => $index, 'name' => $name], $sheetNames, array_keys($sheetNames)),
                'active_sheet' => $sheetIndex,
                'columns' => $columns,
                'rows' => $rows,
                'total_rows' => $highestRow,
                'total_columns' => $highestColumn,
                'visible_columns' => $columnCount,
                'offset' => $offset,
                'next_offset' => $endRow < $highestRow ? $endRow : null,
                'merged_cells' => array_slice(array_values($worksheet->getMergeCells()), 0, 1000),
                'truncated_columns' => $highestColumn > $maxColumns,
            ];

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            return $result;
        } catch (\Throwable $exception) {
            report($exception);
            throw ValidationException::withMessages(['file' => trans('media::file-manager.spreadsheet_invalid')]);
        }
    }

    /** @return array<string, mixed> */
    private function archive(string $path, Media $item, array $options): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw ValidationException::withMessages(['file' => trans('media::file-manager.zip_unavailable')]);
        }

        $zip = new ZipArchive;
        if ($zip->open($path, ZipArchive::RDONLY) !== true) {
            throw ValidationException::withMessages(['file' => trans('media::file-manager.zip_invalid')]);
        }

        $offset = max(0, (int) ($options['offset'] ?? 0));
        $limit = max(50, min((int) ($options['limit'] ?? 300), 500));
        $maxEntries = max(500, (int) config('file-manager.preview.archive.max_entries', 10000));
        $actualTotal = $zip->numFiles;
        $total = min($actualTotal, $maxEntries);
        $end = min($total, $offset + $limit);
        $entries = [];
        $uncompressed = 0;
        $compressed = 0;
        $fileCount = 0;
        $folderCount = 0;
        $encryptedCount = 0;

        for ($index = 0; $index < $total; $index++) {
            $stat = $zip->statIndex($index, ZipArchive::FL_UNCHANGED);
            if (! is_array($stat)) {
                continue;
            }
            $uncompressed += (int) ($stat['size'] ?? 0);
            $compressed += (int) ($stat['comp_size'] ?? 0);
            $entryPath = $this->utf8((string) ($stat['name'] ?? ''));
            $directory = str_ends_with($entryPath, '/');
            $encrypted = ((int) ($stat['encryption_method'] ?? 0)) !== 0;
            $directory ? $folderCount++ : $fileCount++;
            if ($encrypted) {
                $encryptedCount++;
            }

            if ($index < $offset || $index >= $end || $index >= $maxEntries) {
                continue;
            }

            $cleanPath = rtrim($entryPath, '/');
            $entries[] = [
                'path' => $entryPath,
                'name' => basename(str_replace('\\', '/', $cleanPath)),
                'directory' => $directory,
                'depth' => max(0, substr_count(trim($cleanPath, '/'), '/')),
                'extension' => $directory ? null : mb_strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION)),
                'size' => (int) ($stat['size'] ?? 0),
                'compressed_size' => (int) ($stat['comp_size'] ?? 0),
                'encrypted' => $encrypted,
            ];
        }

        $zip->close();

        return [
            'kind' => 'archive',
            'name' => $item->name,
            'entries' => $entries,
            'total_entries' => $actualTotal,
            'total_uncompressed' => $uncompressed,
            'total_compressed' => $compressed,
            'file_count' => $fileCount,
            'folder_count' => $folderCount,
            'encrypted_count' => $encryptedCount,
            'offset' => $offset,
            'next_offset' => $end < $total ? $end : null,
            'truncated' => $actualTotal > $maxEntries,
        ];
    }

    private function rgb(?string $argb): ?string
    {
        if (! is_string($argb) || ! preg_match('/^[0-9A-F]{6,8}$/i', $argb)) {
            return null;
        }

        return '#'.strtoupper(substr($argb, -6));
    }

    private function utf8(string $value): string
    {
        return mb_check_encoding($value, 'UTF-8') ? $value : mb_convert_encoding($value, 'UTF-8', 'CP437,Windows-1256,ISO-8859-1');
    }
}
