<?php

namespace JobMetric\Media\Services;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Exceptions\MediaMimeTypeNotInGroupsException;
use JobMetric\Media\Exceptions\MediaTypeNotMatchException;
use JobMetric\Media\Facades\Media as MediaFacade;
use JobMetric\Media\Jobs\RemoveOldConvertedFileJobs;
use JobMetric\Media\Models\Media;
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
}
