<?php

namespace JobMetric\Media\Object;

use Exception;
use JMedia;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Events\UploadFileEvent;
use JobMetric\Media\Exceptions\DiskNotDefinedException;
use JobMetric\Media\Exceptions\DuplicateFileException;
use JobMetric\Media\Exceptions\FileNotSendInRequestException;
use JobMetric\Media\Exceptions\FolderNotFoundException;
use JobMetric\Media\Models\Media;
use JobMetric\Media\Models\MediaPath;
use Throwable;

class File
{
    private static File $instance;

    private ?string $name = null;
    private ?string $disk = null;
    private ?string $filename = null;
    private ?string $mime_type = null;
    private int $size = 0;
    private ?string $content_id = null;
    private array $responsive = [];
    private string $collection = 'public';

    /**
     * get instance object
     *
     * @return File
     */
    public static function getInstance(): File
    {
        if(empty(File::$instance)) {
            File::$instance = new File;
        }

        return File::$instance;
    }

    public function setMedia(Media $media): void
    {
        $this->name = $media->name;
        $this->disk = $media->disk;
        $this->filename = $media->filename;
        $this->mime_type = $media->mime_type;
        $this->size = $media->size;
        $this->content_id = $media->content_id;

        $additional = json_decode($media->additional, true);
        if(isset($additional['responsive'])) {
            $this->responsive = $additional['responsive'];
        }

        $this->collection = $media->collection;
    }

    /**
     * upload file
     *
     * @param int|null $folder
     * @param string   $collection
     * @param string   $field
     * @param string   $disk
     *
     * @return Media
     * @throws Throwable
     */
    public function upload(int $folder = null, string $collection = 'public', string $field = 'file', string $disk = 'default'): Media
    {
        if(!request()->exists($field)) {
            throw new FileNotSendInRequestException($field);
        }

        $file = request()->file($field);

        $content_id = sha1($file->getContent());
        if(!config('jmedia.collections.'.$collection.'.duplicate_content')) {
            if(Media::query()->where([
                'collection' => $collection,
                'content_id' => $content_id
            ])->exists()) {
                throw new DuplicateFileException;
            }
        }

        if(!JMedia::category()->exist($folder, $collection)) {
            throw new FolderNotFoundException;
        }

        if($disk == 'default') {
            $disk = config('jmedia.collections.'.$collection.'.disk');
        }

        if(!array_key_exists($disk, config('filesystems.disks'))) {
            throw new DiskNotDefinedException($disk);
        }

        $original_name = $file->getClientOriginalName();
        $mime_type = $file->getMimeType();
        $size = $file->getSize();
        $extension = $file->extension();
        $filename = uuid_create().'.'.$extension;

        try {
            $file->storeAs($collection, $filename, $disk);
        } catch(Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode());
        }

        $additional['user_id'] = auth()->check() ? auth()->id() : 0;

        /**
         * @var Media $object
         */
        $object = Media::query()->create([
            'name'       => $original_name,
            'disk'       => $disk,
            'filename'   => $filename,
            'parent_id'  => $folder,
            'type'       => MediaTypeEnum::FILE->value,
            'mime_type'  => $mime_type,
            'size'       => $size,
            'content_id' => $content_id,
            'additional' => $additional,
            'collection' => $collection,
        ]);

        // Hierarchical Data Closure Table Pattern
        $level = 0;

        $paths = MediaPath::query()->where('media_id', $folder)->orderBy('level')->get();
        foreach($paths as $path) {
            MediaPath::query()->create([
                'media_id' => $folder,
                'path_id' => $path->path_id,
                'level' => $level++
            ]);
        }

        MediaPath::query()->create([
            'media_id' => $object->id,
            'path_id' => $object->id,
            'level' => $level
        ]);

        event(new UploadFileEvent($object));

        return $object;
    }

    /**
     * download file
     *
     * @return void
     */
    public function download(): void
    {

    }

    /**
     * stream download file
     *
     * @return void
     */
    public function stream(): void
    {

    }

    /**
     * details file
     *
     * @return void
     */
    public function details(): void
    {

    }

    /**
     * use at in model
     *
     * @return void
     */
    public function useAt(): void
    {

    }

    /**
     * delete file
     *
     * @return void
     */
    public function delete(): void
    {

    }

    /**
     * rename category
     *
     * @return void
     */
    public function rename(): void
    {

    }
}
