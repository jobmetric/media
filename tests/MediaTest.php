<?php

namespace JobMetric\Media\Tests;

use JobMetric\Media\Exceptions\MediaFolderNameInvalidException;
use JobMetric\Media\Exceptions\MediaNotFoundException;
use JobMetric\Media\Facades\Media;
use JobMetric\Media\Http\Resources\MediaResource;
use Tests\BaseDatabaseTestCase as BaseTestCase;
use Throwable;

class MediaTest extends BaseTestCase
{
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
            $this->assertInstanceOf(MediaFolderNameInvalidException::class, $e);
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
            'type' => 'folder'
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

        // create a new folder with parent folder
        $mediaChild = Media::newFolder('b', $media['data']->id);

        $this->assertIsArray($mediaChild);
        $this->assertTrue($mediaChild['ok']);
        $this->assertEquals($mediaChild['message'], trans('media::base.messages.created', [
            'type' => 'folder'
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
}
