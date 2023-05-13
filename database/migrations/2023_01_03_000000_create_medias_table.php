<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->id();

            $table->string('name')->index();
            $table->string('disk');
            $table->uuid()->unique();

            $table->foreignId('parent_id')->constrained('medias')->restrictOnDelete()->cascadeOnUpdate();
            $table->enum('type', [
                'c', // category
                'f'  // file
            ])->index();

            $table->string('mime_type')->nullable()->index();
            $table->unsignedBigInteger('size')->default(0)->index();
            /**
             * if type=c -> size=0 else size>=0
             */

            $table->json('additional')->nullable();
            /**
             * user_id
             * responsive
             * set icon for folder
             */

            $table->string('collection')->default('public')->index();
            /**
             * value: public, avatar, ...
             */

            $table->softDeletes();
            $table->nullableTimestamps();
        });

        Schema::create('media_path', function (Blueprint $table) {
            $table->foreignId('media_id')->index()->constrained('medias')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('path_id')->index()->constrained('medias')->restrictOnDelete()->cascadeOnUpdate();
            $table->integer('level')->default(0);

            $table->unique([
                'media_id',
                'path_id'
            ], 'MEDIA_PATH_UNIQUE');
        });

        Schema::create('media_relations', function (Blueprint $table) {
            $table->morphs('relatable');
            /**
             * relatable to: any model
             */

            $table->string('collection');
            $table->foreignId('media_id')->index()->constrained('medias')->cascadeOnUpdate()->cascadeOnDelete();

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
        Schema::dropIfExists('medias');
        Schema::dropIfExists('media_path');
        Schema::dropIfExists('media_relations');
    }
};
