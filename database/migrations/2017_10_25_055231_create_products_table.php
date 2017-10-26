<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the table
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->integer('price')->unsigned();
        });

        // Insert some entries

        DB::table('products')->insert([

            [
                'name' => 'Bread',
                'description' => 'Freshly baked bread right out of an oven',
                'price' => 50,
            ],
            [
                'name' => 'Cheese',
                'description' => 'Freshly baked bread right out of an oven',
                'price' => 150,
            ],
            [
                'name' => 'Chocolate',
                'description' => 'Freshly baked bread right out of an oven',
                'price' => 100,
            ]

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
