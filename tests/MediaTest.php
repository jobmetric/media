<?php

namespace JobMetric\Media\Tests;

use Illuminate\Support\Facades\Storage;
use JobMetric\Media\Enums\MediaTypeEnum;
use JobMetric\Media\Exceptions\MediaMustInSameFolderException;
use JobMetric\Media\Exceptions\MediaNameInvalidException;
use JobMetric\Media\Exceptions\MediaNotFoundException;
use JobMetric\Media\Exceptions\MediaSameNameException;
use JobMetric\Media\Facades\Media;
use JobMetric\Media\Http\Resources\MediaRelationResource;
use JobMetric\Media\Http\Resources\MediaResource;
use Throwable;

class MediaTest extends BaseMedia
{
    /**
     * @throws Throwable
     */
    public function test_pagination()
    {
    }

    /**
     * @throws Throwable
     */
    public function test_all()
    {
    }

    /**
     * @throws Throwable
     */
    public function test_new_folder()
    {
        // test invalid char for name folder
        try {
            $media = Media::newFolder('_test_');

            $this->assertIsArray($media);
        } catch (Throwable $e) {
            $this->assertInstanceOf(MediaNameInvalidException::class, $e);
        }

        // test invalid parent folder
        try {
            $media = Media::newFolder('test', 999);

            $this->assertIsArray($media);
        } catch (Throwable $e) {
            $this->assertInstanceOf(MediaNotFoundException::class, $e);
        }

        // create a new folder
        $media = Media::newFolder('a');

        $this->assertIsArray($media);
        $this->assertTrue($media['ok']);
        $this->assertEquals($media['message'], trans('media::base.messages.created', [
            'type' => trans('media::base.media_type.folder')
        ]));
        $this->assertInstanceOf(MediaResource::class, $media['data']);
        $this->assertEquals(201, $media['status']);

        $this->assertDatabaseHas(config('media.tables.media'), [
            'name' => 'a',
            'parent_id' => null,
            'type' => 'c'
        ]);

        $this->assertDatabaseHas(config('media.tables.media_path'), [
            'media_id' => $media['data']->id,
            'path_id' => $media['data']->id,
            'level' => 0
        ]);

        // check the duplicate folder
        try {
            $media = Media::newFolder('a');

            $this->assertIsArray($media);
        } catch (Throwable $e) {
            $this->assertInstanceOf(MediaSameNameException::class, $e);
        }

        // create a new folder with parent folder
        $mediaChild = Media::newFolder('b', $media['data']->id);

        $this->assertIsArray($mediaChild);
        $this->assertTrue($mediaChild['ok']);
        $this->assertEquals($mediaChild['message'], trans('media::base.messages.created', [
            'type' => trans('media::base.media_type.folder')
        ]));
        $this->assertInstanceOf(MediaResource::class, $mediaChild['data']);
        $this->assertEquals(201, $mediaChild['status']);

        $this->assertDatabaseHas(config('media.tables.media'), [
            'name' => 'b',
            'parent_id' => $mediaChild['data']->parent_id,
            'type' => 'c'
        ]);

        $this->assertDatabaseHas(config('media.tables.media_path'), [
            'media_id' => $mediaChild['data']->id,
            'path_id' => $media['data']->id,
            'level' => 0
        ]);

        $this->assertDatabaseHas(config('media.tables.media_path'), [
            'media_id' => $mediaChild['data']->id,
            'path_id' => $mediaChild['data']->id,
            'level' => 1
        ]);
    }

    /**
     * @throws Throwable
     */
    public function test_rename()
    {
        // create a new folder
        $media_1 = Media::newFolder('a');
        Media::newFolder('b');

        // test invalid char for name folder
        try {
            $media_rename = Media::rename($media_1['data']->id, '_test_');

            $this->assertIsArray($media_rename);
        } catch (Throwable $e) {
            $this->assertInstanceOf(MediaNameInvalidException::class, $e);
        }

        // test invalid media
        try {
            $media_rename = Media::rename(999, 'test');

            $this->assertIsArray($media_rename);
        } catch (Throwable $e) {
            $this->assertInstanceOf(MediaNotFoundException::class, $e);
        }

        // check exist folder name
        try {
            $media_rename = Media::rename($media_1['data']->id, 'b');

            $this->assertIsArray($media_rename);
        } catch (Throwable $e) {
            $this->assertInstanceOf(MediaSameNameException::class, $e);
        }

        // rename folder
        $media_rename = Media::rename($media_1['data']->id, 'c');

        $this->assertIsArray($media_rename);
        $this->assertTrue($media_rename['ok']);
        $this->assertEquals($media_rename['message'], trans('media::base.messages.rename', [
            'type' => trans('media::base.media_type.folder'),
        ]));
        $this->assertInstanceOf(MediaResource::class, $media_rename['data']);
        $this->assertEquals(200, $media_rename['status']);
    }

    /**
     * @throws Throwable
     */
    public function test_has_folder()
    {
        // create a new folder
        $media = Media::newFolder('a');

        // test invalid media
        try {
            $media_has_folder = Media::hasFolder(999);

            $this->assertIsBool($media_has_folder);
        } catch (Throwable $e) {
            $this->assertInstanceOf(MediaNotFoundException::class, $e);
        }

        // check folder
        $media_has_folder = Media::hasFolder($media['data']->id);

        $this->assertTrue($media_has_folder);
    }

    /**
     * @throws Throwable
     */
    public function test_has_file()
    {
    }

    /**
     * @throws Throwable
     */
    public function test_upload()
    {
        $image = $this->create_image();

        // send file in request
        $response = $this->post(route('media.upload'), [
            'file' => $image
        ]);

        $response->assertStatus(201);

        $this->assertTrue($response->json('ok'));
        $this->assertEquals($response->json('message'), trans('media::base.messages.created', [
            'type' => trans('media::base.media_type.file')
        ]));
        $this->assertEquals(201, $response->json('status'));

        $data = $response->json('data');

        // check path file
        Storage::disk($data['disk'])->assertExists($data['collection'] . '/' . $data['filename']);

        // check exist file in the path
        $this->assertTrue(Storage::disk($data['disk'])->exists($data['collection'] . '/' . $data['filename']));

        $this->assertDatabaseHas(config('media.tables.media'), [
            'name' => $data['name'],
            'parent_id' => null,
            'type' => MediaTypeEnum::FILE(),
            'mime_type' => $data['mime_type'],
            'size' => $data['size'],
            'content_id' => $data['content_id'],
            'disk' => $data['disk'],
            'collection' => $data['collection'],
            'extension' => $data['extension'],
        ]);

        $this->assertDatabaseHas(config('media.tables.media_path'), [
            'media_id' => $data['id'],
            'path_id' => $data['id'],
            'level' => 0
        ]);

        // remove test file
        Storage::disk($data['disk'])->delete($data['collection'] . '/' . $data['filename']);
    }

    /**
     * @throws Throwable
     */
    public function test_download()
    {
        $image = $this->create_image();

        // upload file
        $responseUpload = $this->post(route('media.upload'), [
            'file' => $image
        ]);

        // download file
        $responseDownload = $this->get(route('media.download', $responseUpload->json('data')['id']));

        $responseDownload->assertStatus(200);

        $this->assertEquals('image/jpeg', $responseDownload->headers->get('content-type'));

        // remove test file
        Storage::disk($responseUpload->json('data')['disk'])->delete($responseUpload->json('data')['collection'] . '/' . $responseUpload->json('data')['filename']);

    }

    /**
     * @throws Throwable
     */
    public function test_temporary_url()
    {
    }

    /**
     * @throws Throwable
     */
    public function test_stream()
    {
    }

    /**
     * @throws Throwable
     */
    public function test_details()
    {
        $image = $this->create_image();

        // send file in request
        $response = $this->post(route('media.upload'), [
            'file' => $image
        ]);

        $dataResponse = $response->json();

        // get details
        $responseDetails = $this->get(route('media.details', $dataResponse['data']['id']));

        $responseDetails->assertStatus(200);

        $dataDetails = $responseDetails->json();

        $this->assertTrue($dataDetails['ok']);
        $this->assertEquals($dataDetails['message'], trans('media::base.messages.details', [
            'type' => trans('media::base.media_type.file'),
        ]));
        $this->assertEquals(200, $dataDetails['status']);

        // remove test file
        Storage::disk($dataResponse['data']['disk'])->delete($dataResponse['data']['collection'] . '/' . $dataResponse['data']['filename']);
    }

    /**
     * @throws Throwable
     */
    public function test_used_in()
    {
        $image = $this->create_image();

        // send file in request
        $response = $this->post(route('media.upload'), [
            'file' => $image
        ]);

        $dataResponse = $response->json();

        // store product
        $product = $this->create_product();

        $product->attachMedia($dataResponse['data']['id']);

        // get used in
        $dataUsedIns = Media::usedIn($dataResponse['data']['id']);

        $this->assertCount(1, $dataUsedIns);

        $dataUsedIns->each(function ($dataUsedIn) {
            $this->assertInstanceOf(MediaRelationResource::class, $dataUsedIn);
        });

        // remove test file
        Storage::disk($dataResponse['data']['disk'])->delete($dataResponse['data']['collection'] . '/' . $dataResponse['data']['filename']);
    }

    /**
     * @throws Throwable
     */
    public function test_has_used()
    {
        $image = $this->create_image();

        // send file in request
        $response = $this->post(route('media.upload'), [
            'file' => $image
        ]);

        $dataResponse = $response->json();

        // store product
        $product = $this->create_product();

        // check has used in
        $dataHasUsed = Media::hasUsed($dataResponse['data']['id']);

        $this->assertFalse($dataHasUsed);

        // attach media
        $product->attachMedia($dataResponse['data']['id']);

        // check has used in
        $dataHasUsed = Media::hasUsed($dataResponse['data']['id']);

        $this->assertTrue($dataHasUsed);

        // remove test file
        Storage::disk($dataResponse['data']['disk'])->delete($dataResponse['data']['collection'] . '/' . $dataResponse['data']['filename']);
    }

    /**
     * @throws Throwable
     */
    public function test_compress()
    {
        $file_1 = $this->create_image();
        $file_2 = $this->create_image();
        $file_3 = $this->create_image();
        $file_4 = $this->create_image();

        $folder_a = Media::newFolder('a');
        $folder_a1 = Media::newFolder('a1', $folder_a['data']->id);
        $folder_b = Media::newFolder('b');

        $response_file_1 = $this->post(route('media.upload'), [
            'file' => $file_1,
            'parent_id' => $folder_a['data']->id
        ])->json();

        $response_file_2 = $this->post(route('media.upload'), [
            'file' => $file_2,
            'parent_id' => $folder_a1['data']->id
        ])->json();

        $response_file_3 = $this->post(route('media.upload'), [
            'file' => $file_3,
            'parent_id' => $folder_b['data']->id
        ])->json();

        $response_file_4 = $this->post(route('media.upload'), [
            'file' => $file_4
        ])->json();

        // check file selected in different folder
        try {
            Media::compress([
                $response_file_1['data']['id'],
                $response_file_2['data']['id'],
                $response_file_3['data']['id'],
                $response_file_4['data']['id']
            ], 'test');
        } catch (Throwable $e) {
            $this->assertInstanceOf(MediaMustInSameFolderException::class, $e);
        }

        $compress = Media::compress([
            $folder_a['data']['id'],
            $folder_b['data']['id'],
            $response_file_4['data']['id']
        ], 'test');

        $this->assertTrue($compress['ok']);
        $this->assertEquals($compress['message'], trans('media::base.messages.zipped'));
        $this->assertInstanceOf(MediaResource::class, $compress['data']);
        $this->assertEquals(201, $compress['status']);

        $this->assertDatabaseHas(config('media.tables.media'), [
            'name' => 'test',
            'parent_id' => null,
            'type' => MediaTypeEnum::FILE(),
            'mime_type' => 'application/zip',
            'content_id' => $compress['data']->content_id,
            'disk' => $compress['data']->disk,
            'collection' => $compress['data']->collection,
            'extension' => 'zip',
        ]);

        // check exist file in the path
        $this->assertTrue(Storage::disk($compress['data']->disk)->exists($compress['data']->filename));

        // remove test file
        Storage::disk($compress['data']->disk)->delete($compress['data']->filename);
    }

    /**
     * @throws Throwable
     */
    public function test_extract()
    {
    }

    /**
     * @throws Throwable
     */
    public function test_move()
    {
    }

    /**
     * @throws Throwable
     */
    public function test_delete()
    {
    }

    /**
     * @throws Throwable
     */
    public function test_restore()
    {
    }

    /**
     * @throws Throwable
     */
    public function test_force_delete()
    {
    }
}
