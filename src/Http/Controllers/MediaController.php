<?php

namespace JobMetric\Media\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JobMetric\Media\Facades\Media;
use JobMetric\Media\Http\Controllers\Controller as BaseMediaController;
use JobMetric\Media\Http\Requests\CompressRequest;
use JobMetric\Media\Http\Requests\DetailsRequest;
use JobMetric\Media\Http\Requests\NewFolderRequest;
use JobMetric\Media\Http\Requests\RenameRequest;
use JobMetric\Media\Http\Requests\UploadRequest;
use JobMetric\Media\Models\Media as MediaModel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class MediaController extends BaseMediaController
{
    /**
     * index media
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $page_limit = $request->input('page_limit', 50);
        $with = $request->input('with', []);
        $mode = $request->input('mode');

        if ($request->has('filter.parent_id') && $request->input('filter.parent_id') === 'null') {
            $request->merge(['filter' => ['parent_id' => null]]);
        }

        $filter = $request->input('filter', []);

        if ($page_limit == -1) {
            $media = Media::all($filter, $with, $mode);
        } else {
            $media = Media::paginate($filter, $page_limit, $with, $mode);
        }

        return $this->responseCollection($media);
    }

    /**
     * New Folder
     *
     * @param NewFolderRequest $request
     *
     * @return JsonResponse
     */
    public function newFolder(NewFolderRequest $request): JsonResponse
    {
        return $this->response(
            Media::newFolder($request->name, $request->parent_id)
        );
    }

    /**
     * Upload a new media
     *
     * @param UploadRequest $request
     *
     * @return JsonResponse
     */
    public function upload(UploadRequest $request): JsonResponse
    {
        return $this->response(
            Media::upload($request->parent_id, $request->collection)
        );
    }

    /**
     * Download the media
     *
     * @param MediaModel $media
     *
     * @return StreamedResponse
     */
    public function download(MediaModel $media): StreamedResponse
    {
        return Media::download($media->id);
    }

    /**
     * Get Details the media
     *
     * @param MediaModel $media
     * @param DetailsRequest $request
     *
     * @return JsonResponse
     */
    public function details(MediaModel $media, DetailsRequest $request): JsonResponse
    {
        return $this->response(
            Media::details($media->id, $request->with ?? [])
        );
    }

    /**
     * Rename the media
     *
     * @param MediaModel $media
     * @param RenameRequest $request
     *
     * @return JsonResponse
     */
    public function rename(MediaModel $media, RenameRequest $request): JsonResponse
    {
        try {
            return $this->response(
                Media::rename($media->id, $request->name)
            );
        } catch (Throwable $exception) {
            return $this->response(message: $exception->getMessage(), status: $exception->getCode());
        }
    }

    /**
     * Compress media
     *
     * @param CompressRequest $request
     *
     * @return JsonResponse
     */
    public function compress(CompressRequest $request): JsonResponse
    {
        return $this->response(
            Media::compress($request->media_ids, $request->name)
        );
    }
}
