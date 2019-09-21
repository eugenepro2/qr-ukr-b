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
            $table->integer('projects_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->foreign('projects_id')->references('id')->on('projects');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->foreign('packages_id')->references('id')->on('packages');
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
