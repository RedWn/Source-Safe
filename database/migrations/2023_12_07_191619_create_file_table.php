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

        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('serverPath');
            
            $table->foreignId('projectID')->constrained('projects');
            $table->foreignId('folderID')->constrained('files');
            $table->foreignId('checkedInBy')->nullable()->constrained('users');

            $table->timestamps();
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
        Schema::dropIfExists('files');
    }
}
