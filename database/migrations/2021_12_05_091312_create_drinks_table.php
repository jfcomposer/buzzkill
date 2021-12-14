<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drinks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->integer('mg_caffeine_per_serving');
            $table->float('servings_per_container');
        });

        DB::table('drinks')->insert([
            ['name' => 'Monster Ultra Sunrise', 'mg_caffeine_per_serving' => 75, 'servings_per_container' => 2],
            ['name' => 'Black Coffee', 'mg_caffeine_per_serving' => 95, 'servings_per_container' => 1],
            ['name' => 'Americano', 'mg_caffeine_per_serving' => 77, 'servings_per_container' => 1],
            ['name' => 'Sugar Free NOS', 'mg_caffeine_per_serving' => 130, 'servings_per_container' => 2],
            ['name' => '5 Hour Energy', 'mg_caffeine_per_serving' => 200, 'servings_per_container' => 1]
        ]);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drinks');
    }
}
