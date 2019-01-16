<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('identifier')->comment('phone number or push token or email or ....');
            $table->string('title')->nullable();
            $table->text('body');
            $table->boolean('type')->comment('0 -> sms, 1 -> push, 2 -> message, 3 -> email');
            $table->string('provider_name');
            $table->datetime('expire_at')->nullable();
            $table->datetime('queued_at')->nullable();
            $table->datetime('success_at')->nullable();
            $table->smallInteger('try')->default(1);
            $table->text('error')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notifications');
    }
}
