<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSimpleWeatherTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('simple_weather', function (Blueprint $table) {
            $table->increments('id');
            $table->string('temp');
            $table->string('condition');
            $table->date('record_date');
            $table->string('location_identifier');
            $table->nullableTimestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}