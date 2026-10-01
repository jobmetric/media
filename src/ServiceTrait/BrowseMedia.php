<?php

namespace JobMetric\Media\ServiceTrait;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Models\Media;
use JobMetric\Media\Models\MediaPath;

/**
 * Provides the reusable queries required by graphical file managers.
 */
trait BrowseMedia
{
    /**
     * Browse one folder or search the complete media tree.
     *
     * @param array<string, mixed> $filters
     *
     * @return LengthAwarePaginator
     */
    public function browse(array $filters = []): LengthAwarePaginator
    {
        $parentId = isset($filters['parent_id']) ? (int) $filters['parent_id'] : null;
        $search = trim((string) ($filters['search'] ?? ''));
        $query = Media::query();

        if (($filters['scope'] ?? 'folder') !== 'global' || $search === '') {
            $query->where('parent_id', $parentId);
        }
        if ($search !== '') {
            $query->where('name', 'like', '%'.$search.'%');
        }

        $this->applyMediaGroup($query, $filters['group'] ?? null);

        $sort = in_array($filters['sort'] ?? null, ['name', 'created_at', 'updated_at', 'type'], true)
            ? $filters['sort']
            : 'name';
        $direction = ($filters['direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = max(12, min(100, (int) ($filters['per_page'] ?? 48)));

        return $query->orderByRaw('CASE WHEN type = ? THEN 0 ELSE 1 END', [MediaTypeEnum::FOLDER()])
            ->orderBy($sort, $direction)
            ->orderBy('id')
            ->paginate($perPage);
    }

    /**
     * Return the ordered ancestor chain for a folder.
     *
     * @param int|null $folderId
     *
     * @return Collection<int, Media>
     */
    public function breadcrumbs(?int $folderId): Collection
    {
        if (!$folderId) {
            return collect();
        }

        return MediaPath::query()
            ->with('path')
            ->where('media_id', $folderId)
            ->orderBy('level')
            ->get()
            ->pluck('path')
            ->filter()
            ->values();
    }

    /**
     * Apply a common file group while keeping folders navigable.
     *
     * @param Builder $query
     * @param string|null $group
     *
     * @return void
     */
    private function applyMediaGroup(Builder $query, ?string $group): void
    {
        if (!in_array($group, ['image', 'video', 'audio', 'document', 'archive'], true)) {
            return;
        }

        $query->where(function (Builder $items) use ($group): void {
            $items->where('type', MediaTypeEnum::FOLDER())
                ->orWhere(function (Builder $files) use ($group): void {
                    $files->where('type', MediaTypeEnum::FILE());
                    if (in_array($group, ['image', 'video', 'audio'], true)) {
                        $files->where('mime_type', 'like', $group.'/%');
                    } elseif ($group === 'archive') {
                        $files->whereIn('extension', ['zip', 'rar', '7z', 'tar', 'gz']);
                    } else {
                        $files->where('mime_type', 'not like', 'image/%')
                            ->where('mime_type', 'not like', 'video/%')
                            ->where('mime_type', 'not like', 'audio/%')
                            ->whereNotIn('extension', ['zip', 'rar', '7z', 'tar', 'gz']);
                    }
                });
        });
    }
}
