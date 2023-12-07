<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('file', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->unsignedBigInteger('projectID')->index();
            $table->foreign('projectID')->references('id')->on('project');
            $table->unsignedBigInteger('folderID')->index()->nullable();
            $table->foreign('folderID')->references('id')->on('folder');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file');
    }
}
