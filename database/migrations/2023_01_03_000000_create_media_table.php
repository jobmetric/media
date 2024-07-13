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
        Schema::create(config('media.tables.media'), function (Blueprint $table) {
            $table->id();

            $table->string('name')->index();

            $table->foreignId('parent_id')->nullable()->constrained('media')->restrictOnDelete()->cascadeOnUpdate();

            $table->enum('type', MediaTypeEnum::values())->index();
            /**
             * value: 'c' for folder, 'f' for file
             *
             * use: @extends MediaTypeEnum
             */

            $table->string('mime_type')->nullable()->index();
            /**
             * value: image/jpeg, application/pdf, ...
             */

            $table->unsignedBigInteger('size')->default(0)->index();
            /**
             * base on byte
             *
             * if the type=c -> size=0 else size>=0
             */

            $table->string('content_id', 40)->nullable()->unique()->index();
            /**
             * sha1 content file unique
             *
             * if the type=c -> content_id=null else content_id=sha1(file)
             */

            $table->json('additional')->nullable();
            /**
             * user_id: 0 -> anonymous, x>0 -> user
             * responsive
             * set icon for folder
             */

            $table->string('disk');
            /**
             * value: public, s3, ...
             */

            $table->string('collection')->default('public')->index();
            /**
             * value: public, avatar, ...
             */

            $table->string('filename')->nullable()->unique();
            /**
             * filename = uuid + . + extension
             *
             * if the type=c -> filename=null else filename=uuid.extension
             */

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('media.tables.media'));
    }
};
