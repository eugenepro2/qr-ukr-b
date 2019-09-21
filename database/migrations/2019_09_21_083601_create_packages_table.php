<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('name')->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('from_project')->unsigned()->nullable();
            $table->integer('to_project')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->foreign('from_project')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('to_project')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->foreign('packages_id')->references('id')->on('packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
