<?php

namespace JobMetric\Media\Services;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Exceptions\MediaMimeTypeNotInGroupsException;
use JobMetric\Media\Exceptions\MediaNotFoundException;
use JobMetric\Media\Exceptions\MediaTypeNotMatchException;
use JobMetric\Media\Facades\Media as MediaFacade;
use JobMetric\Media\Jobs\RemoveOldConvertedFileJobs;
use JobMetric\Media\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class MediaImage
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected Application $app;

    /**
     * Create a new Translation instance.
     *
     * @param Application $app
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * convert image to webp
     *
     * @param Media $media
     * @param bool $force_replace
     *
     * @return void
     * @throws Throwable
     */
    public function convertToWebp(Media $media, bool $force_replace = false): void
    {
        ini_set('memory_limit', '-1');

        try {
            if ($media->type === MediaTypeEnum::FOLDER()) {
                throw new MediaTypeNotMatchException($media->id, MediaTypeEnum::FILE());
            }

            if (getMimeGroup($media->mime_type) !== 'image') {
                throw new MediaMimeTypeNotInGroupsException($media->id, 'image');
            }

            $file_path = MediaFacade::getMediaPath($media);
            $file_path = Storage::disk($media->disk)->path($file_path);

            $file_type = exif_imagetype($file_path);
            switch ($file_type) {
                // IMAGE TYPE JPEG
                case '2':
                    $image = imagecreatefromjpeg($file_path);

                    $input_extension = ['jpeg', 'JPEG', 'jpg', 'JPG'];
                    break;

                // IMAGE TYPE PNG
                case '3':
                    $image = imagecreatefrompng($file_path);
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);

                    $input_extension = ['png', 'PNG'];
                    break;

                // IMAGE TYPE BMP
                case '6':
                    $image = imagecreatefrombmp($file_path);

                    $input_extension = ['bmp', 'BMP'];
                    break;
                default:
                    throw new Exception("Unsupported image format.");
            }

            if (!$image) {
                throw new Exception("Failed to create image from file.");
            }

            $flag = false;
            $output_file_path = '';
            $file_path_part = explode('.', $file_path);
            $extension = end($file_path_part);
            foreach ($input_extension as $item) {
                if ($item == $extension) {
                    $output_file_path = Str::replaceLast($item, 'webp', $file_path);
                    $flag = true;
                    break;
                }
            }

            if (!$flag) {
                throw new Exception("Unsupported image format.");
            }

            if (file_exists($output_file_path) && !$force_replace) {
                throw new Exception("WebP file already exists.");
            }

            if (!imagewebp($image, $output_file_path, config('media.webp_convert.quality'))) {
                throw new Exception("Failed to convert image to WebP.");
            }

            imagedestroy($image);

            $media->mime_type = mime_content_type($output_file_path);
            $media->size = filesize($output_file_path);
            $media->content_id = sha1_file($output_file_path);
            $media->extension = 'webp';
            $media->save();

            RemoveOldConvertedFileJobs::dispatch($media, $extension)->delay(now()->addMinutes(5));
        } catch (Exception $e) {
            Log::error('Image conversion failed: ' . $e->getMessage());
        }
    }

    /**
     * Responsive image or resize image
     *
     * @param string $media_uuid
     * @param int|null $width
     * @param int|null $height
     *
     * @return JsonResponse|BinaryFileResponse|StreamedResponse
     * @throws Throwable
     */
    public function responsive(string $media_uuid, int $width = null, int $height = null): JsonResponse|BinaryFileResponse|StreamedResponse
    {
        /**
         * @var Media $media
         */
        $media = Media::findByUuid($media_uuid);

        if (!$media) {
            throw new MediaNotFoundException($media_uuid);
        }

        if ($media->type === MediaTypeEnum::FOLDER()) {
            throw new MediaTypeNotMatchException($media->id, MediaTypeEnum::FILE());
        }

        if (getMimeGroup($media->mime_type) !== 'image') {
            throw new MediaMimeTypeNotInGroupsException($media->id, 'image');
        }

        $original_file_path = MediaFacade::getMediaPath($media);

        if (!Storage::disk($media->disk)->exists($original_file_path)) {
            throw new MediaNotFoundException($media_uuid);
        }

        if ($width && $height) {
            $cache_file_path = $this->getCachePath($media, $width, $height);
            $cache_folder_path = $this->getCachePath($media, isFolder: true);

            if (Storage::disk($media->disk)->exists($cache_file_path)) {
                return response()->download(Storage::disk($media->disk)->path($cache_file_path));
            } else {
                // make resize image
                $file_path = Storage::disk($media->disk)->path($original_file_path);

                [$original_width, $original_height] = getimagesize($file_path);

                $file_type = exif_imagetype($file_path);
                $image = match ($file_type) {
                    2 => imagecreatefromjpeg($file_path),
                    3 => imagecreatefrompng($file_path),
                    6 => imagecreatefrombmp($file_path),
                    18 => imagecreatefromwebp($file_path),
                    default => throw new Exception("Unsupported image format."),
                };

                if (!$image) {
                    throw new Exception("Failed to create image from file.");
                }

                $scale = $width / $original_width;

                $new_width = $width;
                $new_height = (int)($original_height * $scale);

                $new_image = imagecreatetruecolor($new_width, $new_height);

                if ($media->mime_type == 'image/png' || $media->mime_type == 'image/webp') {
                    imagealphablending($new_image, false);
                    imagesavealpha($new_image, true);
                }

                imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);

                imagedestroy($image);

                // make cache folder
                Storage::disk($media->disk)->makeDirectory($cache_folder_path);

                $output_file_path = Storage::disk($media->disk)->path($cache_file_path);

                if (!imagewebp($new_image, $output_file_path, config('media.webp_convert.quality'))) {
                    throw new Exception("Failed to convert image to WebP.");
                }

                // save new image
                Storage::disk($media->disk)->put($cache_file_path, file_get_contents($output_file_path));

                imagedestroy($new_image);

                return response()->download($output_file_path, $media->name . '-' . $width . '-' . $height . '.' . 'webp');
            }
        }

        return response()->download(Storage::disk($media->disk)->path($original_file_path), $media->name . '.' . $media->extension);
    }

    /**
     * Get cache path file or folder
     *
     * @param Media $media
     * @param int|null $width
     * @param int|null $height
     * @param bool $isFolder
     *
     * @return string
     */
    private function getCachePath(Media $media, int $width = null, int $height = null, bool $isFolder = false): string
    {
        if ($isFolder) {
            return 'cache/' . $media->collection . '/' . substr($media->created_at, 0, 4) . '/' . substr($media->created_at, 5, 2);
        }

        return 'cache/' . $media->collection . '/' . substr($media->created_at, 0, 4) . '/' . substr($media->created_at, 5, 2) . '/' . $media->uuid . '-' . $width . '-' . $height . '.' . $media->extension;
    }
}
