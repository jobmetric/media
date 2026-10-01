<?php

namespace JobMetric\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use JobMetric\Media\Events\NewFolderEvent;
use JobMetric\Media\Events\UploadFileEvent;
use JobMetric\Media\Exceptions\FileManagerConflict;
use JobMetric\Media\Models\Media;
use JobMetric\Media\Models\MediaPath;
use JobMetric\Media\Models\MediaRelation;
use RuntimeException;
use Throwable;
use ZipArchive;

/** Storage-neutral file-manager operations on the package's existing media tree. */
class FileManager
{
    /** Resolve a virtual root or an existing folder. */
    public function folder(?int $id): ?Media
    {
        return $id ? Media::query()->where('type', 'c')->findOrFail($id) : null;
    }

    /** Store a validated uploaded file without depending on the HTTP request singleton. */
    public function upload(UploadedFile $file, ?int $parentId = null, ?string $resolution = null, bool $allowDuplicate = false, bool $confirmUsed = false): Media
    {
        $this->folder($parentId);
        $name = $this->validName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();
        $configuration = config('media.collections.public');
        $allowed = array_merge(...array_values(config('media.mime_type', [])));
        if (! in_array($mime, $allowed, true) || in_array($extension, ['php', 'phtml', 'phar', 'html', 'htm', 'js', 'svg'], true)) {
            throw ValidationException::withMessages(['file' => trans('media::file-manager.unsupported')]);
        }
        $limit = (int) ($configuration['max_size'] ?? -1);
        if ($limit > 0 && $file->getSize() > $limit) {
            throw ValidationException::withMessages(['file' => trans('media::file-manager.too_large')]);
        }
        $hash = sha1_file($file->getPathname());
        if (! $allowDuplicate) {
            $same = Media::query()->where('collection', 'public')->where(function ($query) use ($hash): void {
                $query->where('content_id', $hash)->orWhere('info->checksum', $hash);
            })->first();
            if ($same) {
                throw new FileManagerConflict($same->parent_id === $parentId ? 'duplicate_same_folder' : 'duplicate_other_folders', ['existing' => $same]);
            }
        }

        return DB::transaction(function () use ($file, $parentId, $resolution, $confirmUsed, $name, $extension, $mime, $configuration, $hash): Media {
            $existing = $this->collision($parentId, $name, $extension);
            if ($existing && $resolution === 'skip') {
                return $existing;
            }
            $name = $this->resolveName($existing, $name, $parentId, $extension, $resolution, $confirmUsed);
            $disk = ($configuration['disk'] ?? 'public') === 'default' ? config('filesystems.default') : $configuration['disk'];
            $uuid = (string) Str::uuid();
            $path = 'public/'.date('Y/m').'/'.$uuid.'.'.$extension;
            $stream = fopen($file->getPathname(), 'rb');
            try {
                if (! Storage::disk($disk)->put($path, $stream)) {
                    throw new RuntimeException('Unable to persist uploaded file.');
                }
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
            try {
                $contentId = Media::withTrashed()->where('content_id', $hash)->exists() ? sha1($hash.$uuid) : $hash;
                $attributes = ['name' => $name, 'parent_id' => $parentId, 'type' => 'f', 'mime_type' => $mime, 'extension' => $extension, 'size' => $file->getSize(), 'content_id' => $contentId, 'info' => ['checksum' => $hash], 'disk' => $disk, 'collection' => 'public', 'uuid' => $uuid];
                // Preserve the ID and relations when explicitly replacing used media.
                if ($existing && $resolution === 'replace') {
                    $existing->fill($attributes);
                    $existing->created_at = now();
                    $existing->save();
                    $media = $existing;
                } else {
                    $media = Media::query()->create($attributes);
                }
                $this->rebuildPaths($media);
                event(new UploadFileEvent($media));

                return $media;
            } catch (Throwable $error) {
                Storage::disk($disk)->delete($path);
                throw $error;
            }
        });
    }

    /** Create a Unicode-named folder and its closure paths. */
    public function createFolder(string $name, ?int $parentId): Media
    {
        $this->folder($parentId);
        $name = $this->validName($name);
        if ($existing = $this->collision($parentId, $name, null)) {
            throw new FileManagerConflict('name_conflict', ['destination' => $existing]);
        }

        return DB::transaction(function () use ($name, $parentId): Media {
            $folder = Media::query()->create(['name' => $name, 'parent_id' => $parentId, 'type' => 'c']);
            $this->rebuildPaths($folder);
            event(new NewFolderEvent($folder));

            return $folder;
        });
    }

    /** Rename only the display name, retaining the content and stable references. */
    public function rename(Media $item, string $name): Media
    {
        if ($item->type === 'f' && str_ends_with(strtolower($name), '.'.strtolower($item->extension))) {
            $name = substr($name, 0, -strlen($item->extension) - 1);
        }
        $name = $this->validName($name);
        $existing = $this->collision($item->parent_id, $name, $item->extension);
        if ($existing && $existing->id !== $item->id) {
            throw new FileManagerConflict('name_conflict', ['destination' => $existing]);
        }
        $item->update(['name' => $name]);

        return $item;
    }

    /** Copy or move trees atomically; reject cycles and resolve destination collisions. */
    public function paste(array $ids, ?int $destination, string $mode, ?string $resolution = null, bool $confirmUsed = false): array
    {
        $this->folder($destination);
        if (! in_array($mode, ['copy', 'move'], true)) {
            throw new \InvalidArgumentException('Invalid paste mode.');
        }

        return DB::transaction(function () use ($ids, $destination, $mode, $resolution, $confirmUsed): array {
            $processed = [];
            $skipped = [];
            foreach ($this->topLevel($ids) as $item) {
                if ($item->id === $destination || ($destination && MediaPath::query()->where('media_id', $destination)->where('path_id', $item->id)->exists())) {
                    throw ValidationException::withMessages(['destination_id' => trans('media::file-manager.cycle')]);
                }
                if ($mode === 'move' && $item->parent_id === $destination) {
                    $skipped[] = $item->id;

                    continue;
                }
                $existing = $this->collision($destination, $item->name, $item->extension);
                if ($existing && $resolution === 'skip') {
                    $skipped[] = $item->id;

                    continue;
                }
                $name = $this->resolveName($existing, $item->name, $destination, $item->extension, $resolution, $confirmUsed);
                if ($existing && $resolution === 'replace') {
                    // Replacing a tree in use must never discard its descendants' references.
                    $this->delete([$existing->id]);
                }
                if ($mode === 'copy') {
                    $result = $this->copyTree($item, $destination, $name);
                } else {
                    $item->update(['parent_id' => $destination, 'name' => $name]);
                    $this->rebuildTree($item);
                    $result = $item;
                }
                $processed[] = $result->id;
            }

            return compact('processed', 'skipped');
        });
    }

    /** Soft-delete unused trees only; retain physical objects for recoverability. */
    public function delete(array $ids): void
    {
        DB::transaction(function () use ($ids): void {
            foreach ($this->topLevel($ids) as $item) {
                $tree = $this->treeIds($item);
                if (MediaRelation::query()->whereIn('media_id', $tree)->exists()) {
                    throw new FileManagerConflict('item_in_use', ['existing' => $item]);
                }
                Media::query()->whereIn('id', $tree)->delete();
            }
        });
    }

    /** Create a ZIP using virtual folder names rather than physical storage paths. */
    public function compress(array $ids, string $name, ?int $destination): Media
    {
        $name = $this->validName(preg_replace('/\.zip$/i', '', $name));
        $temporary = tempnam(sys_get_temp_dir(), 'media-zip-');
        $zip = new ZipArchive;
        try {
            if ($zip->open($temporary, ZipArchive::OVERWRITE) !== true) {
                throw new RuntimeException('Cannot create archive.');
            }
            foreach ($this->topLevel($ids) as $item) {
                $this->appendZip($zip, $item, $this->displayName($item));
            }
            $zip->close();

            return $this->upload(new UploadedFile($temporary, $name.'.zip', 'application/zip', null, true), $destination, null, true);
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }
    }

    /** Extract bounded ZIP entries without allowing paths outside the virtual tree. */
    public function extract(Media $item, string $destination): array
    {
        if ($item->extension !== 'zip') {
            throw ValidationException::withMessages(['file' => trans('media::file-manager.unsupported')]);
        }
        $temporary = tempnam(sys_get_temp_dir(), 'media-extract-');
        $zip = new ZipArchive;
        try {
            file_put_contents($temporary, Storage::disk($item->disk)->get($this->path($item)));
            if ($zip->open($temporary) !== true || $zip->numFiles > 2000) {
                throw ValidationException::withMessages(['file' => trans('media::file-manager.archive_limit')]);
            }
            $entries = [];
            $bytes = 0;
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $stat = $zip->statIndex($index);
                $name = str_replace('\\', '/', $stat['name']);
                $bytes += $stat['size'];
                if ($bytes > 256 * 1024 * 1024 || preg_match('#(^/|(^|/)\.\.(/|$)|^[A-Za-z]:|\x00)#', $name)) {
                    throw ValidationException::withMessages(['file' => trans('media::file-manager.archive_limit')]);
                }
                $entries[] = $name;
            }

            return DB::transaction(function () use ($zip, $entries, $item, $destination): array {
                $parent = $destination === 'named_folder' ? $this->createFolder($item->name, $item->parent_id) : $this->folder($item->parent_id);
                $count = 0;
                $skipped = 0;
                foreach ($entries as $name) {
                    $parts = explode('/', trim($name, '/'));
                    $fileName = array_pop($parts);
                    $folder = $parent;
                    foreach ($parts as $part) {
                        $folder = Media::query()->where('parent_id', $folder?->id)->where('type', 'c')->where('name', $part)->first() ?? $this->createFolder($part, $folder?->id);
                    }
                    if (str_ends_with($name, '/')) {
                        if (! $this->collision($folder?->id, $fileName, null)) {
                            $this->createFolder($fileName, $folder?->id);
                        }

                        continue;
                    }
                    $path = tempnam(sys_get_temp_dir(), 'media-entry-');
                    try {
                        $stream = $zip->getStream($name);
                        $out = fopen($path, 'wb');
                        if (! $stream || ! $out) {
                            throw new RuntimeException('Cannot read archive entry.');
                        }
                        stream_copy_to_stream($stream, $out, 256 * 1024 * 1024);
                        fclose($stream);
                        fclose($out);
                        $this->upload(new UploadedFile($path, $fileName, null, null, true), $folder?->id, 'skip', true);
                        $count++;
                    } catch (ValidationException $error) {
                        $skipped++;
                    } finally {
                        if (is_file($path)) {
                            unlink($path);
                        }
                    }
                }

                return ['destination' => $parent, 'extracted_count' => $count, 'skipped_count' => $skipped];
            });
        } finally {
            $zip->close();
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }
    }

    /** Resolve the package storage key. */
    public function path(Media $item): string
    {
        return $item->collection.'/'.$item->created_at->format('Y/m').'/'.$item->uuid.'.'.$item->extension;
    }

    /** Return the display filename including extension. */
    public function displayName(Media $item): string
    {
        return $item->name.($item->type === 'f' && $item->extension ? '.'.$item->extension : '');
    }

    /** Reject path separators, control characters and platform-reserved names. */
    private function validName(string $name): string
    {
        $name = trim($name);
        if ($name === '' || mb_strlen($name) > 200 || preg_match('/[\x00-\x1f<>:"\/\\\\|?*]/u', $name) || in_array($name, ['.', '..'], true)) {
            throw ValidationException::withMessages(['name' => trans('media::file-manager.invalid_name')]);
        }

        return $name;
    }

    /** Find a sibling with the same visible filename. */
    private function collision(?int $parent, string $name, ?string $extension): ?Media
    {
        return Media::query()->where('parent_id', $parent)->where('name', $name)->where('extension', $extension)->first();
    }

    /** Resolve overwrite, skip and keep-both choices. */
    private function resolveName(?Media $existing, string $name, ?int $parent, ?string $extension, ?string $resolution, bool $confirmUsed): string
    {
        if (! $existing) {
            return $name;
        }
        if ($resolution === 'keep_both') {
            for ($index = 2; $this->collision($parent, $name.' ('.$index.')', $extension); $index++);

            return $name.' ('.$index.')';
        }
        if ($resolution !== 'replace') {
            throw new FileManagerConflict('name_conflict', ['destination' => $existing]);
        }
        if (! $confirmUsed && MediaRelation::query()->whereIn('media_id', $this->treeIds($existing))->exists()) {
            throw new FileManagerConflict('replace_used_confirmation', ['destination' => $existing]);
        }

        return $name;
    }

    /** Select roots only when an ancestor and child are both selected. */
    private function topLevel(array $ids): array
    {
        $items = Media::query()->whereIn('id', $ids)->get();
        if ($items->count() !== count(array_unique($ids))) {
            throw ValidationException::withMessages(['item_ids' => trans('media::file-manager.missing')]);
        }

        return $items->filter(fn (Media $item) => ! MediaPath::query()->where('media_id', $item->id)->whereIn('path_id', array_diff($ids, [$item->id]))->exists())->all();
    }

    /** Find descendants using the package closure table. */
    private function treeIds(Media $item): array
    {
        return array_unique(array_merge([$item->id], MediaPath::query()->where('path_id', $item->id)->pluck('media_id')->all()));
    }

    /** Rebuild paths following a virtual move. */
    private function rebuildTree(Media $item): void
    {
        $this->rebuildPaths($item);
        foreach (Media::query()->where('parent_id', $item->id)->get() as $child) {
            $this->rebuildTree($child);
        }
    }

    /** Synchronize one record's ancestor path. */
    private function rebuildPaths(Media $item): void
    {
        MediaPath::query()->where('media_id', $item->id)->delete();
        $paths = $item->parent_id ? MediaPath::query()->where('media_id', $item->parent_id)->orderBy('level')->pluck('path_id')->all() : [];
        $paths[] = $item->id;
        foreach ($paths as $level => $path) {
            MediaPath::query()->create(['media_id' => $item->id, 'path_id' => $path, 'level' => $level]);
        }
    }

    /** Copy records while sharing immutable content; subsequent replacement uses a new UUID. */
    private function copyTree(Media $item, ?int $parent, string $name): Media
    {
        $copy = $item->replicate();
        $copy->name = $name;
        $copy->parent_id = $parent;
        $copy->uuid = (string) Str::uuid();
        if ($item->type === 'f') {
            $copy->content_id = sha1($item->content_id.$copy->uuid);
            $copy->info = array_merge($item->info ?? [], ['checksum' => $item->info['checksum'] ?? $item->content_id]);
            $copy->created_at = now();
            if (! Storage::disk($item->disk)->copy($this->path($item), $this->path($copy))) {
                throw new RuntimeException('Cannot copy media content.');
            }
        }
        $copy->save();
        $this->rebuildPaths($copy);
        foreach (Media::query()->where('parent_id', $item->id)->get() as $child) {
            $this->copyTree($child, $copy->id, $child->name);
        }

        return $copy;
    }

    /** Append an existing virtual tree to a ZIP. */
    private function appendZip(ZipArchive $zip, Media $item, string $name): void
    {
        if ($item->type === 'c') {
            $zip->addEmptyDir($name);
            foreach (Media::query()->where('parent_id',$item->id)->get() as $child) {
                $this->appendZip($zip,$child,$name.'/'.$this->displayName($child));
            }
        } else {
            $zip->addFromString($name,Storage::disk($item->disk)->get($this->path($item)));
        }
    }
}
