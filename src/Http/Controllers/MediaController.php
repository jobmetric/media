<?php

namespace JobMetric\Media\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JobMetric\Media\Exceptions\MediaNotFoundException;
use JobMetric\Media\Facades\Media;
use JobMetric\Media\Facades\MediaImage;
use JobMetric\Media\Http\Controllers\Controller as BaseMediaController;
use JobMetric\Media\Http\Requests\CompressRequest;
use JobMetric\Media\Http\Requests\DeleteRequest;
use JobMetric\Media\Http\Requests\DetailsRequest;
use JobMetric\Media\Http\Requests\ImageResponsiveRequest;
use JobMetric\Media\Http\Requests\NewFolderRequest;
use JobMetric\Media\Http\Requests\RenameRequest;
use JobMetric\Media\Http\Requests\UploadRequest;
use JobMetric\Media\Models\Media as MediaModel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

        try {
            return $this->responseCollection($media);
        } catch (Throwable $exception) {
            return $this->response(message: $exception->getMessage(), status: $exception->getCode());
        }
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
        try {
            return $this->response(
                Media::newFolder($request->name, $request->parent_id)
            );
        } catch (Throwable $exception) {
            return $this->response(message: $exception->getMessage(), status: $exception->getCode());
        }
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
        try {
            return $this->response(
                Media::upload($request->parent_id, $request->collection)
            );
        } catch (Throwable $exception) {
            return $this->response(message: $exception->getMessage(), status: $exception->getCode());
        }
    }

    /**
     * Download the media
     *
     * @param MediaModel $media
     *
     * @return JsonResponse|StreamedResponse
     */
    public function download(MediaModel $media): JsonResponse|StreamedResponse
    {
        try {
            return Media::download($media->id);
        } catch (Throwable $exception) {
            return $this->response(message: $exception->getMessage(), status: $exception->getCode());
        }

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
        try {
            return $this->response(
                Media::details($media->id, $request->with ?? [])
            );
        } catch (Throwable $exception) {
            return $this->response(message: $exception->getMessage(), status: $exception->getCode());
        }
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
     * Delete the media
     *
     * @param DeleteRequest $request
     *
     * @return JsonResponse
     */
    public function delete(DeleteRequest $request): JsonResponse
    {
        try {
            if ($request->mode === 'normal') {
                return $this->response(
                    Media::delete($request->ids, $request->parent_id)
                );
            } else {
                // trash mode
                return $this->response(
                    Media::forceDelete($request->ids, $request->parent_id)
                );
            }
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
        try {
            return $this->response(
                Media::compress($request->media_ids, $request->name)
            );
        } catch (Throwable $exception) {
            return $this->response(message: $exception->getMessage(), status: $exception->getCode());
        }
    }

    /**
     * Responsive image
     *
     * @param ImageResponsiveRequest $request
     *
     * @return JsonResponse|BinaryFileResponse|StreamedResponse
     * @throws Throwable
     */
    public function responsive(ImageResponsiveRequest $request): JsonResponse|BinaryFileResponse|StreamedResponse
    {
        try {
            return MediaImage::responsive($request->uuid, $request->w, $request->h, $request->m);
        } catch (MediaNotFoundException $exception) {
            return $this->response(message: $exception->getMessage(), status: $exception->getCode());
        }
    }
}
