<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JobMetric\Media\Enums\MediaTypeEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            $table->string('name')->index();

            $table->foreignId('parent_id')->nullable()->constrained('media')->restrictOnDelete()->cascadeOnUpdate();
            $table->enum('type', [
                'c', // folder
                'f'  // file
            ])->index();
            /**
             * value: folder, file
             * use: @extends MediaTypeEnum
             */

            $table->string('mime_type')->nullable()->index();
            $table->unsignedBigInteger('size')->default(0)->index();
            /**
             * if type=c -> size=0 else size>=0
             */

            $table->string('content_id', 40)->nullable()->unique()->index();
            /**
             * sha1 content file unique
             */

            $table->json('additional')->nullable();
            /**
             * user_id : 0 -> anonymous , x>0 -> user
             * responsive
             * set icon for folder
             */

            $table->string('disk');
            $table->string('collection')->default('public')->index();
            /**
             * value: public, avatar, ...
             */

            $table->string('filename')->unique();
            /**
             * filename = uuid + . + extension
             */

            $table->softDeletes();
            $table->nullableTimestamps();
        });

        Schema::create('media_paths', function (Blueprint $table) {
            $table->id();

            $table->foreignId('media_id')->index()->constrained('media')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('path_id')->index()->constrained('media')->restrictOnDelete()->cascadeOnUpdate();
            $table->integer('level')->default(0);

            $table->unique([
                'media_id',
                'path_id'
            ], 'MEDIA_PATH_UNIQUE');
        });

        // if media:type=f insert in media_relations
        Schema::create('media_relations', function (Blueprint $table) {
            $table->morphs('relatable');
            /**
             * relatable to: any model
             */

            $table->string('collection');
            $table->foreignId('media_id')->nullable()->index()->constrained('media')->cascadeOnUpdate()->cascadeOnDelete();

            $table->unique([
                'relatable_type',
                'relatable_id',
                'collection',
                'media_id'
            ], 'FILE_RELATION_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('media_path');
        Schema::dropIfExists('media_relations');
    }
};
