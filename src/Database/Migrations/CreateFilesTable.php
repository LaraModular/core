<?php

namespace LaraModule\Core\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique();
            $table->string('name_original', 255)->nullable();
            $table->string('folder', 255);
            $table->string('mime', 50);
            $table->string('disk', 50)->default('public');
            $table->unsignedBigInteger('total_size');
            $table->unsignedBigInteger('uploaded_size')->default(0);
            $table->enum('status', ['completed', 'failed', 'paused', 'uploading', 'initial']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
