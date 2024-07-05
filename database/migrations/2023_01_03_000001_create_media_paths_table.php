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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('media_path');
    }
};
