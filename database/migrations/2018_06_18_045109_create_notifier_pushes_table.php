<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifierPushesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("notifier_pushes", function (Blueprint $table) {
            $table->increments("id");
            $table->string("provider");
            $table->string("player_id");
            $table->string("heading");
            $table->longText("content");
            $table->json("extra")->nullable();
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
        Schema::dropIfExists("notifier_pushes");
    }
}
