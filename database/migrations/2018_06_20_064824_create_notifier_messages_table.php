<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifierMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("notifier_messages", function (Blueprint $table) {
            $table->increments("id");
            $table->string("provider");
            $table->string("user_id");
            $table->string("title");
            $table->longText("body");
            $table->string("status");
            $table->string("result_id")->nullable();
            $table->json("description")->nullable();
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
        Schema::dropIfExists("notifier_smses");
    }
}
