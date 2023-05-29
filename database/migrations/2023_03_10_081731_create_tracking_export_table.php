<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tracking_export', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('batch_process_chunk_id')->nullable();
            $table->string('batch_write_csv_id')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: exporting, 1: exported');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_export');
    }
};
