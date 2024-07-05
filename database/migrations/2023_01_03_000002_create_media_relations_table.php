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
        // if media:type=f insert in media_relations
        Schema::create('media_relations', function (Blueprint $table) {
            $table->foreignId('media_id')->nullable()->index()->constrained('media')->cascadeOnUpdate()->cascadeOnDelete();

            $table->morphs('mediaable');
            /**
             * mediaable to: any model
             */

            $table->string('collection')->index();

            $table->unique([
                'media_id',
                'mediaable_type',
                'mediaable_id',
                'collection'
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
        Schema::dropIfExists('media_relations');
    }
};
