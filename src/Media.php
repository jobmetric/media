<?php

namespace JobMetric\Media;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Events\UploadFileEvent;
use JobMetric\Media\Exceptions\DiskNotDefinedException;
use JobMetric\Media\Exceptions\DuplicateFileException;
use JobMetric\Media\Exceptions\FileNotSendInRequestException;
use JobMetric\Media\Exceptions\FolderNotFoundException;
use JobMetric\Media\Http\Resources\MediaResource;
use JobMetric\Media\Models\Media as MediaModel;
use JobMetric\Media\Models\MediaPath;
use Spatie\QueryBuilder\QueryBuilder;

class Media
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
     * Get the specified media.
     *
     * @param array $filter
     * @param array $with
     *
     * @return QueryBuilder
     */
    private function query(array $filter = [], array $with = []): QueryBuilder
    {
        $fields = [
            'id',
            'name',
            'parent_id',
            'type',
            'mime_type',
            'size',
            'content_id',
            'additional',
            'disk',
            'collection',
            'filename',
            'deleted_at',
            'created_at',
            'updated_at'
        ];

        $query = QueryBuilder::for(MediaModel::class)
            ->allowedFields($fields)
            ->allowedSorts($fields)
            ->allowedFilters($fields)
            ->defaultSort([
                'type',
                '-created_at',
                'name'
            ])
            ->where($filter);

        if (!empty($with)) {
            $query->with($with);
        }

        return $query;
    }

    /**
     * Paginate the specified media.
     *
     * @param array $filter
     * @param int $page_limit
     * @param array $with
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(array $filter = [], int $page_limit = 15, array $with = []): AnonymousResourceCollection
    {
        return MediaResource::collection(
            $this->query($filter, $with)->paginate($page_limit)
        );
    }

    /**
     * Get all media.
     *
     * @param array $filter
     * @param array $with
     *
     * @return AnonymousResourceCollection
     */
    public function all(array $filter = [], array $with = []): AnonymousResourceCollection
    {
        return MediaResource::collection(
            $this->query($filter, $with)->get()
        );
    }

    /**
     * New Folder
     *
     * @param string $name
     * @param int $parent_id
     *
     * @return array
     */
    public function newFolder(string $name, int $parent_id = 0): array
    {
    }

    /**
     * Rename media
     *
     * @param int $media_id
     * @param string $name
     *
     * @return array
     */
    public function rename(int $media_id, string $name): array
    {
    }

    /**
     * Has Folder
     *
     * @param int $media_id
     *
     * @return bool
     */
    public function hasFolder(int $media_id): bool
    {
    }

    /**
     * Upload media
     *
     * @param int|null $folder
     * @param string $collection
     * @param string $field
     * @param string $disk
     *
     * @return array
     */
    public function upload(int $folder = null, string $collection = 'public', string $field = 'file', string $disk = 'default'): array
    {
        if(!request()->exists($field)) {
            throw new FileNotSendInRequestException($field);
        }

        $file = request()->file($field);

        $content_id = sha1($file->getContent());
        if(!config('jmedia.collections.'.$collection.'.duplicate_content')) {
            if(\JobMetric\Media\Models\Media::query()->where([
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
     * Download media
     *
     * @param int $media_id
     *
     * @return array
     */
    public function download(int $media_id): array
    {
    }

    /**
     * Stream media
     *
     * @param int $media_id
     *
     * @return array
     */
    public function stream(int $media_id): array
    {
    }

    /**
     * Details media
     *
     * @param int $media_id
     *
     * @return array
     */
    public function details(int $media_id): array
    {
    }

    /**
     * Use At media
     *
     * @param int $media_id
     *
     * @return array
     */
    public function useAt(int $media_id): array
    {
    }

    /**
     * Compress media
     *
     * @param array $media_ids
     *
     * @return array
     */
    public function compress(array $media_ids): array
    {
    }

    /**
     * Extract media
     *
     * @param int $media_id
     *
     * @return array
     */
    public function extract(int $media_id): array
    {
    }

    /**
     * Share media
     *
     * @param int $media_id
     *
     * @return array
     */
    public function share(int $media_id): array
    {
    }

    /**
     * Copy media
     *
     * @param int $media_id
     *
     * @return array
     */
    public function copy(int $media_id): array
    {
    }

    /**
     * Cut media
     *
     * @param int $media_id
     *
     * @return array
     */
    public function cut(int $media_id): array
    {
    }

    /**
     * Paste media
     *
     * @param int $media_id
     *
     * @return array
     */
    public function paste(int $media_id): array
    {
    }

    /**
     * Get Path media
     *
     * @param int $media_id
     *
     * @return array
     */
    public function getPath(int $media_id): array
    {
    }

    /**
     * Delete media
     *
     * @param int $media_id
     *
     * @return array
     */
    public function delete(int $media_id): array
    {
    }

    /**
     * Restore media
     *
     * @param int $media_id
     *
     * @return array
     */
    public function restore(int $media_id): array
    {
    }

    /**
     * Force Delete media
     *
     * @param int $media_id
     *
     * @return array
     */
    public function forceDelete(int $media_id): array
    {
    }
}
