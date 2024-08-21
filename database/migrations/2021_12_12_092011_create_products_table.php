<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('price');
            $table->float('main_price');
            $table->string('expired_date');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('user_id');
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->integer('counter')->default(0);
            $table->integer('likes')->default(0);
            $table->string('img_url')->nullable();
            $table->integer('sum');
            $table->integer('price_1_d');
            $table->integer('price_2_d');
            $table->float('disC1');
            $table->float('disC2');
            $table->float('disC3');
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
        Schema::dropIfExists('products');
    }
}
