<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckinTable extends Migration
{
    /**
 * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('checkins', function (Blueprint $table) {
            $table->id();
            $table->date('checkout_date');
            $table->boolean('done')->default(false);

            $table->foreignId('file_id')->constrained();
            $table->foreignId('user_id')->constrained();

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
        Schema::dropIfExists('checkins');
    }
}
