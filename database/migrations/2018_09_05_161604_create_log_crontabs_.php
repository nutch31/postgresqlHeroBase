<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogCrontabs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::connection('mysql')->create('log_crontabs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 255)->nullable();
            $table->integer('skip')->nullable();
            $table->integer('take')->nullable();
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('log_crontabs');
    }
}
