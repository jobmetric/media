<?php

namespace JobMetric\Media\ServiceTrait;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use JobMetric\Media\Http\Resources\MediaResource;
use JobMetric\Media\Models\Media;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Trait ListMedia
 *
 * @package JobMetric\Media\ServiceTrait
 */
trait ListMedia
{
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
            'info',
            'disk',
            'collection',
            'uuid',
            'extension',
            'deleted_at',
            'created_at',
            'updated_at'
        ];

        $query = QueryBuilder::for(Media::class);

        if ($mode === 'withTrashed') {
            $query->withTrashed();
        }

        if ($mode === 'onlyTrashed') {
            $query->onlyTrashed();
        }

        $allowed_filters = [
            AllowedFilter::callback('user_id', function (Builder $q, $value) {
                $q->whereJsonContains('info', ['user_id' => (int)$value]);
            }),
        ];

        if (isset($filter['user_id'])) {
            unset($filter['user_id']);
        }

        $query->allowedFields($fields)
            ->allowedSorts($fields)
            ->allowedFilters(array_merge($fields, $allowed_filters))
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
}
