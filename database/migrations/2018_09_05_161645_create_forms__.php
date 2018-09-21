<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('forms', function (Blueprint $table) {            
            $table->increments('id');
            $table->unsignedInteger('log_crontab_id')->nullable();
            $table->integer('form_id')->nullable();
            $table->integer('channel_id')->nullable();
            $table->text('name')->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->text('custom_attributes')->nullable();
            $table->integer('is_duplicated')->nullable();
            $table->string('ip', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->string('created_at_forms', 255)->nullable();
            $table->string('updated_at_forms', 255)->nullable();
            $table->string('page_url', 255)->nullable();
            $table->integer('status_log')->nullable();
            $table->string('status_response',10)->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
            
            $table->foreign('log_crontab_id')->references('id')->on('log_crontabs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forms');
    }
}
