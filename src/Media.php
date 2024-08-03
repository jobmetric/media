<?php

namespace JobMetric\Media;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Events\NewFolderEvent;
use JobMetric\Media\Events\UploadFileEvent;
use JobMetric\Media\Exceptions\DiskNotDefinedException;
use JobMetric\Media\Exceptions\DuplicateFileException;
use JobMetric\Media\Exceptions\FileNotSendInRequestException;
use JobMetric\Media\Exceptions\MediaCollectionNotInConfigException;
use JobMetric\Media\Exceptions\MediaMaxSizeException;
use JobMetric\Media\Exceptions\MediaMimeTypeException;
use JobMetric\Media\Exceptions\MediaNameInvalidException;
use JobMetric\Media\Exceptions\MediaNotFoundException;
use JobMetric\Media\Exceptions\MediaSameNameException;
use JobMetric\Media\Exceptions\MediaTypeNotMatchException;
use JobMetric\Media\Http\Resources\MediaRelationResource;
use JobMetric\Media\Http\Resources\MediaResource;
use JobMetric\Media\Models\Media as MediaModel;
use JobMetric\Media\Models\MediaPath;
use JobMetric\Media\Models\MediaRelation;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

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
     * @param string|null $mode
     *
     * @return QueryBuilder
     */
    private function query(array $filter = [], array $with = [], string $mode = null): QueryBuilder
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
            'uuid',
            'extension',
            'deleted_at',
            'created_at',
            'updated_at'
        ];

        $query = QueryBuilder::for(MediaModel::class);

        if ($mode === 'withTrashed') {
            $query->withTrashed();
        }

        if ($mode === 'onlyTrashed') {
            $query->onlyTrashed();
        }

        $query->allowedFields($fields)
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
     * @param string|null $mode
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(array $filter = [], int $page_limit = 15, array $with = [], string $mode = null): AnonymousResourceCollection
    {
        return MediaResource::collection(
            $this->query($filter, $with, $mode)->paginate($page_limit)
        );
    }

    /**
     * Get all media.
     *
     * @param array $filter
     * @param array $with
     * @param string|null $mode
     *
     * @return AnonymousResourceCollection
     */
    public function all(array $filter = [], array $with = [], string $mode = null): AnonymousResourceCollection
    {
        return MediaResource::collection(
            $this->query($filter, $with, $mode)->get()
        );
    }

    /**
     * New Folder
     *
     * @param string $name
     * @param int|null $parent_id
     *
     * @return array
     * @throws Throwable
     */
    public function newFolder(string $name, int $parent_id = null): array
    {
        if (!$this->isValidFolderName($name)) {
            throw new MediaNameInvalidException('folder', $name);
        }

        if ($parent_id) {
            /**
             * @var MediaModel $parent
             */
            $parent = MediaModel::query()->find($parent_id);

            if (!$parent) {
                throw new MediaNotFoundException($parent_id);
            }

            if ($parent->type != MediaTypeEnum::FOLDER()) {
                throw new MediaTypeNotMatchException($parent_id, MediaTypeEnum::FOLDER());
            }

            unset($parent);
        }

        // check exist name in parent folder
        $exist = MediaModel::query()->where([
            'name' => $name,
            'parent_id' => $parent_id
        ])->exists();

        if ($exist) {
            throw new MediaSameNameException($name);
        }

        /**
         * @var MediaModel $media
         */
        $media = MediaModel::query()->create([
            'name' => $name,
            'parent_id' => $parent_id,
            'type' => MediaTypeEnum::FOLDER(),
        ]);

        $level = 0;

        $paths = MediaPath::query()->select('path_id')->where([
            'media_id' => $parent_id
        ])->orderBy('level')->get()->toArray();

        $paths[] = [
            'path_id' => $media->id
        ];

        foreach ($paths as $path) {
            $mediaPath = new MediaPath;
            $mediaPath->media_id = $media->id;
            $mediaPath->path_id = $path['path_id'];
            $mediaPath->level = $level++;
            $mediaPath->save();

            unset($mediaPath);
        }

        event(new NewFolderEvent($media));

        return [
            'ok' => true,
            'message' => trans('media::base.messages.created', [
                'type' => trans('media::base.media_type.folder'),
            ]),
            'data' => MediaResource::make($media),
            'status' => 201
        ];
    }

    /**
     * Rename media
     *
     * @param int $media_id
     * @param string $name
     *
     * @return array
     * @throws Throwable
     */
    public function rename(int $media_id, string $name): array
    {
        /**
         * @var MediaModel $media
         */
        $media = MediaModel::query()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type != MediaTypeEnum::FOLDER()) {
            $mode = trans('media::base.media_type.folder');
            if (!$this->isValidFolderName($name)) {
                throw new MediaNameInvalidException($mode, $name);
            }
        } else {
            $mode = trans('media::base.media_type.file');
            if (!$this->isValidFileName($name)) {
                throw new MediaNameInvalidException($mode, $name);
            }
        }

        // check exist name in parent folder
        $exist = MediaModel::query()->where([
            'name' => $name,
            'parent_id' => $media->parent_id
        ])->where('id', '!=', $media_id)->exists();

        if ($exist) {
            throw new MediaSameNameException($name);
        }

        $media->name = $name;

        $media->save();

        return [
            'ok' => true,
            'message' => trans('media::base.messages.rename', [
                'type' => $mode,
            ]),
            'data' => MediaResource::make($media),
            'status' => 200
        ];
    }

    /**
     * Has Folder
     *
     * @param int $media_id
     *
     * @return bool
     * @throws Throwable
     */
    public function hasFolder(int $media_id): bool
    {
        /**
         * @var MediaModel $media
         */
        $media = MediaModel::query()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type == MediaTypeEnum::FOLDER()) {
            return true;
        }

        return false;
    }

    /**
     * Has File
     *
     * @param int $media_id
     *
     * @return bool
     * @throws Throwable
     */
    public function hasFile(int $media_id): bool
    {
        /**
         * @var MediaModel $media
         */
        $media = MediaModel::query()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type == MediaTypeEnum::File()) {
            return true;
        }

        return false;
    }

    /**
     * Upload media
     *
     * @param int|null $parent_id
     * @param string $collection
     * @param string $field
     *
     * @return array
     * @throws Throwable
     */
    public function upload(int $parent_id = null, string $collection = 'public', string $field = 'file'): array
    {
        // check parent folder
        if ($parent_id && !$this->hasFolder($parent_id)) {
            throw new MediaNotFoundException($parent_id);
        }

        // check the collection
        $config_collections = config('media.collections');

        if (!array_key_exists($collection, $config_collections)) {
            throw new MediaCollectionNotInConfigException($collection);
        }

        // check file
        if (!request()->hasFile($field)) {
            throw new FileNotSendInRequestException($field);
        }

        $file = request()->file($field);

        // check disk
        if ($config_collections[$collection]['disk'] == 'default') {
            $disk = config('filesystems.default');
        } else {
            $disk = $config_collections[$collection]['disk'];
        }

        if (!array_key_exists($disk, config('filesystems.disks'))) {
            throw new DiskNotDefinedException($disk);
        }

        // check duplicate content
        $content_id = sha1($file->getContent());

        if (!$config_collections[$collection]['duplicate_content']) {
            if (MediaModel::query()->where([
                'collection' => $collection,
                'content_id' => $content_id
            ])->exists()) {
                throw new DuplicateFileException;
            }
        }

        // check size
        $size = $file->getSize();

        if ($config_collections[$collection]['max_size'] != -1) {
            if ($size > $config_collections[$collection]['max_size']) {
                throw new MediaMaxSizeException($config_collections[$collection]['max_size']);
            }
        }

        // check the mime type
        $mime_type = $file->getMimeType();

        $config_mime_types = [];

        if ($config_collections[$collection]['mime_type'] == 'any') {
            foreach (config('media.mime_type') as $type_group) {
                $config_mime_types = array_merge($config_mime_types, $type_group);
            }
        } else {
            $mime_type_parts = explode(',', $config_collections[$collection]['mime_type']);

            foreach ($mime_type_parts as $mime_type_part) {
                $config_mime_types = array_merge($config_mime_types, config('media.mime_type.' . $mime_type_part));
            }
        }

        if (!in_array($mime_type, $config_mime_types)) {
            throw new MediaMimeTypeException($mime_type);
        }

        // check the file name
        $original_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        if (!$this->isValidFileName($original_name)) {
            throw new MediaNameInvalidException(trans('media::base.media_type.file'), $original_name);
        }

        $uuid = (string)Str::uuid();
        $extension = $file->extension();
        $filename = $uuid . '.' . $extension;

        try {
            $file->storeAs($collection, $filename, $disk);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode());
        }

        $additional = [];
        if (auth()->guard()->check()) {
            $additional['user_id'] = auth()->guard()->id();
        }

        /**
         * @var MediaModel $media
         */
        $media = MediaModel::query()->create([
            'name' => $original_name,
            'parent_id' => $parent_id,
            'type' => MediaTypeEnum::FILE(),
            'mime_type' => $mime_type,
            'size' => $size,
            'content_id' => $content_id,
            'additional' => $additional,
            'disk' => $disk,
            'collection' => $collection,
            'uuid' => $uuid,
            'extension' => $extension
        ]);

        $level = 0;

        $paths = MediaPath::query()->select('path_id')->where([
            'media_id' => $parent_id
        ])->orderBy('level')->get()->toArray();

        $paths[] = [
            'path_id' => $media->id
        ];

        foreach ($paths as $path) {
            $mediaPath = new MediaPath;
            $mediaPath->media_id = $media->id;
            $mediaPath->path_id = $path['path_id'];
            $mediaPath->level = $level++;
            $mediaPath->save();

            unset($mediaPath);
        }

        event(new UploadFileEvent($media));

        return [
            'ok' => true,
            'message' => trans('media::base.messages.created', [
                'type' => trans('media::base.media_type.file'),
            ]),
            'data' => MediaResource::make($media),
            'status' => 201
        ];
    }

    /**
     * Download media
     *
     * @param int $media_id
     * @param array $headers
     *
     * @return StreamedResponse
     * @throws Throwable
     */
    public function download(int $media_id, array $headers = []): StreamedResponse
    {
        /**
         * @var MediaModel $media
         */
        $media = MediaModel::query()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type == MediaTypeEnum::FOLDER()) {
            throw new MediaTypeNotMatchException($media_id, 'file');
        }

        return Storage::drive($media->disk)->download($media->collection . '/' . $media->uuid . '.' . $media->extension, $media->name . '.' . $media->extension, $headers);
    }

    /**
     * Temporary url for s3 partition media
     *
     * @param int $media_id
     * @param int $expire_time // minutes
     *
     * @return string
     * @throws Throwable
     */
    public function temporaryUrl(int $media_id, int $expire_time = 60): string
    {
        /**
         * @var MediaModel $media
         */
        $media = MediaModel::query()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type == MediaTypeEnum::FOLDER()) {
            throw new MediaTypeNotMatchException($media_id, 'file');
        }

        return Storage::drive($media->disk)->temporaryUrl($media->collection . '/' . $media->uuid . '.' . $media->extension, now()->addMinutes($expire_time), [
            'ResponseContentType' => 'application/octet-stream',
            'ResponseContentDisposition' => 'attachment; filename=' . $media->name . '.' . $media->extension,
        ]);
    }

    /**
     * Stream media
     *
     * @param int $media_id
     *
     * @return void
     */
    public function stream(int $media_id): void
    {
    }

    /**
     * Details media
     *
     * @param int $media_id
     *
     * @return array
     * @throws Throwable
     */
    public function details(int $media_id): array
    {
        /**
         * @var MediaModel $media
         */
        $media = MediaModel::withTrashed()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        return [
            'ok' => true,
            'message' => trans('media::base.messages.details', [
                'type' => trans('media::base.media_type.' . ($media->type == MediaTypeEnum::FOLDER() ? 'folder' : 'file')),
            ]),
            'data' => MediaResource::make($media),
            'status' => 200
        ];
    }

    /**
     * Used In media
     *
     * @param int $media_id
     *
     * @return array
     * @throws Throwable
     */
    public function usedIn(int $media_id): array
    {
        /**
         * @var MediaModel $media
         */
        $media = MediaModel::withTrashed()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type == MediaTypeEnum::FOLDER()) {
            throw new MediaTypeNotMatchException($media_id, 'file');
        }

        $media_relations = MediaRelation::query()->where([
            'media_id' => $media_id
        ])->get();

        return [
            'ok' => true,
            'message' => trans('media::base.messages.details', [
                'type' => trans('media::base.media_type.file'),
            ]),
            'data' => MediaRelationResource::collection($media_relations),
            'status' => 200
        ];
    }

    /**
     * Has Used media
     *
     * @param int $media_id
     *
     * @return bool
     * @throws Throwable
     */
    public function hasUsed(int $media_id): bool
    {
        /**
         * @var MediaModel $media
         */
        $media = MediaModel::withTrashed()->find($media_id);

        if (!$media) {
            throw new MediaNotFoundException($media_id);
        }

        if ($media->type == MediaTypeEnum::FOLDER()) {
            throw new MediaTypeNotMatchException($media_id, 'file');
        }

        return MediaRelation::query()->where([
            'media_id' => $media_id
        ])->exists();
    }

    /**
     * Compress media
     *
     * @param array $media_ids
     *
     * @return void
     */
    public function compress(array $media_ids): void
    {
    }

    /**
     * Extract media
     *
     * @param int $media_id
     *
     * @return void
     */
    public function extract(int $media_id): void
    {
    }

    /**
     * Move media
     *
     * @param int $media_id
     * @param int|null $parent_id
     *
     * @return void
     */
    public function move(int $media_id, int $parent_id = null): void
    {
    }

    /**
     * Delete media
     *
     * @param int $media_id
     *
     * @return void
     */
    public function delete(int $media_id): void
    {
    }

    /**
     * Restore media
     *
     * @param int $media_id
     *
     * @return void
     */
    public function restore(int $media_id): void
    {
    }

    /**
     * Force Delete media
     *
     * @param int $media_id
     *
     * @return void
     */
    public function forceDelete(int $media_id): void
    {
    }

    /**
     * check folder name is valid
     *
     * @param string $folderName
     *
     * @return false|int
     */
    private function isValidFolderName(string $folderName): bool|int
    {
        $pattern = '/^(?!-)[a-zA-Z0-9]+(-[a-zA-Z0-9]+)*$/';

        return preg_match($pattern, $folderName);
    }

    /**
     * check file name is valid
     *
     * @param string $fileName
     *
     * @return false|int
     */
    private function isValidFileName(string $fileName): bool|int
    {
        $pattern = '/^(?!-)[a-zA-Z0-9]+(-[a-zA-Z0-9]+)*$/';

        return preg_match($pattern, $fileName);
    }
}
