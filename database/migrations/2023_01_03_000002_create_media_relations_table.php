<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create(config('media.tables.media_relation'), function (Blueprint $table) {
            $table->foreignId('media_id')->nullable()->index()->constrained('media')->cascadeOnUpdate()->cascadeOnDelete();

            $table->morphs('mediaable');
            /**
             * mediaable to: any model
             */

            $table->string('collection')->index();

            $table->dateTime('created_at')->index()->default(DB::raw('CURRENT_TIMESTAMP'));

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
        Schema::dropIfExists(config('media.tables.media_relations'));
    }
};
