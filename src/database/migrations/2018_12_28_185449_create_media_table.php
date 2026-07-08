<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMediaTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->increments('user_id');
            $table->increments('category_id');
            $table->string('name', 255);
            $table->boolean('is_featured');
            $table->string('image', 255);
            $table->string('file_path', 255);
            $table->increments('file_type');
            $table->string('file_mime', 255);
            $table->string('file_url', 255);
            $table->datetime('deleted_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('media');
    }
}
