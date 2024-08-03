<?php

namespace JobMetric\Media\Http\Controllers;

use Illuminate\Http\JsonResponse;
use JobMetric\Media\Facades\Media;
use JobMetric\Media\Http\Controllers\Controller as BaseMediaController;
use JobMetric\Media\Http\Requests\UploadRequest;
use JobMetric\Media\Models\Media as MediaModel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends BaseMediaController
{
    /**
     * Upload a new media
     *
     * @param UploadRequest $request
     *
     * @return JsonResponse
     */
    public function upload(UploadRequest $request): JsonResponse
    {
        $media = Media::upload($request->parent_id, $request->collection);

        return response()->json($media, $media['status']);
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
}
