<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotSearchRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_search_requests', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->nullable();
            $table->string('request_received')->nullable();
            $table->string('stage_model')->default('new');
            $table->integer('year_id')->nullable();
            $table->string('year')->nullable();
            $table->integer('make_id')->nullable();
            $table->string('make')->nullable();
            $table->integer('car_model_id')->nullable();
            $table->string('car_model')->nullable();
            $table->integer('component_id')->nullable();
            $table->string('component')->nullable();
            $table->integer('component_category_id')->nullable();
            $table->string('component_category')->nullable();
            $table->boolean('finished')->default(false);
            $table->integer('session')->default(400);
            $table->boolean('terminate')->default(false);
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
        Schema::dropIfExists('bot_search_requests');
    }
}
