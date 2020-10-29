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
            $table->string('year_id')->default();
            $table->string('year')->default();
            $table->string('make_id')->default();
            $table->string('make')->default();
            $table->string('car_model_id')->nullable();
            $table->string('car_model')->nullable();
            $table->string('component_id')->default();
            $table->string('component')->default();
            $table->string('component_category_id')->default();
            $table->string('component_category')->default();
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
