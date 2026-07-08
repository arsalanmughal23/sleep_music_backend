<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePackagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->float('price');
            $table->string('currency', 255);
            $table->increments('product_min_limit');
            $table->increments('product_max_limit');
            $table->string('package_id_ios', 255);
            $table->string('package_id_android', 255);
            $table->boolean('status');
            $table->boolean('is_default');
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
        Schema::drop('packages');
    }
}
