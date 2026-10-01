<?php

namespace JobMetric\Media\Tests;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use JobMetric\Media\Exceptions\FileManagerConflict;
use JobMetric\Media\FileManager;
use JobMetric\Media\MediaServiceProvider;
use JobMetric\Media\Models\Media;
use Orchestra\Testbench\TestCase;

/** Exercise the package file tree independently of any application's database. */
class FileManagerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [MediaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        Storage::fake('media_public');
    }

    public function test_unicode_upload_copy_move_zip_and_duplicate_detection(): void
    {
        $files = new FileManager;
        $folder = $files->createFolder('تصاویر', null);
        $file = UploadedFile::fake()->image('تصویر اصلی.png');
        $media = $files->upload($file, $folder->id);
        $this->assertSame('تصویر اصلی', $media->name);
        Storage::disk('media_public')->assertExists($files->path($media));
        $copy = $files->paste([$media->id], null, 'copy');
        $copied = Media::findOrFail($copy['processed'][0]);
        $this->assertNotSame($media->uuid, $copied->uuid);
        Storage::disk('media_public')->assertExists($files->path($copied));
        $files->paste([$media->id], null, 'move', 'keep_both');
        $this->assertNull($media->fresh()->parent_id);
        $archive = $files->compress([$copied->id], 'archive', null);
        $this->assertSame('zip', $archive->extension);
        $result = $files->extract($archive, 'named_folder');
        $this->assertSame(1, $result['extracted_count']);
        $this->expectException(FileManagerConflict::class);
        $files->upload($file,null);
    }

    public function test_tree_move_rebuilds_descendant_paths_and_rejects_cycles(): void
    {
        $files=new FileManager;
        $first=$files->createFolder('First',null);
        $child=$files->createFolder('Child',$first->id);
        $second=$files->createFolder('Second',null);
        $files->paste([$first->id],$second->id,'move');
        $this->assertDatabaseHas(config('media.tables.media_path'),['media_id'=>$child->id,'path_id'=>$second->id]);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $files->paste([$second->id],$child->id,'move');
    }

    public function test_used_file_replacement_preserves_relations_and_requires_confirmation(): void
    {
        $files=new FileManager;
        $item=$files->upload(UploadedFile::fake()->image('photo.png',12,12));
        \JobMetric\Media\Models\MediaRelation::query()->create(['media_id'=>$item->id,'mediaable_type'=>'TestModel','mediaable_id'=>1,'collection'=>'base']);
        try {
            $files->upload(UploadedFile::fake()->image('photo.png',14,14),null,'replace',true);
            $this->fail('An in-use replacement must require confirmation.');
        } catch(FileManagerConflict $conflict) { $this->assertSame('replace_used_confirmation',$conflict->reason); }
        $updated=$files->upload(UploadedFile::fake()->image('photo.png',14,14),null,'replace',true,true);
        $this->assertSame($item->id,$updated->id);
        $this->assertNotSame($item->uuid,$updated->uuid);
        $this->assertDatabaseHas(config('media.tables.media_relation'),['media_id'=>$item->id]);
        $this->expectException(FileManagerConflict::class);
        $files->delete([$item->id]);
    }

    public function test_zip_traversal_is_rejected_before_creating_any_items(): void
    {
        $files=new FileManager;
        $path=tempnam(sys_get_temp_dir(),'media-test-');
        $zip=new \ZipArchive;
        $zip->open($path,\ZipArchive::OVERWRITE);
        $zip->addFromString('../outside.txt','unsafe');
        $zip->close();
        try {
            $archive=$files->upload(new UploadedFile($path,'unsafe.zip','application/zip',null,true));
            try { $files->extract($archive,'named_folder'); $this->fail('Traversal must be rejected.'); }
            catch(\Illuminate\Validation\ValidationException $error) { $this->assertArrayHasKey('file',$error->errors()); }
            $this->assertSame(1,Media::count());
        } finally { unlink($path); }
    }
}
