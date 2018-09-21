<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('calls', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('log_crontab_id')->nullable();
            $table->integer('call_id')->nullable();
            $table->string('date', 255)->nullable();
            $table->integer('duration')->nullable();
            $table->string('recording_url', 255)->nullable();
            $table->integer('status')->nullable();
            $table->string('phone', 255)->nullable();
            $table->integer('channel_id')->nullable();
            $table->integer('is_duplicated')->nullable();
            $table->string('location', 255)->nullable();
            $table->string('created_at_calls', 255)->nullable();
            $table->string('updated_at_calls', 255)->nullable();
            $table->string('client_number', 255)->nullable();
            $table->string('call_uuid', 255)->nullable();
            $table->integer('call_mapped')->nullable();
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
        Schema::dropIfExists('calls');
    }
}
