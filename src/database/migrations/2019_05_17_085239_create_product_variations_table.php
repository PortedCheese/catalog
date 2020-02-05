<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('product_id')
                ->comment("Товар");
            
            $table->char('sku', 100)
                ->unique()
                ->comment("Артикул");
            
            $table->float('price')
                ->comment("Цена");
            
            $table->float('sale_price')
                ->nullable()
                ->comment("Цена со скидкой");
            
            $table->boolean('sale')
                ->default(0)
                ->comment("Есть скидка");
            
            $table->boolean('available')
                ->default(1)
                ->comment("Наличие вариации");
            
            $table->string('description')
                ->nullable()
                ->comment("Описание");
            
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
        Schema::dropIfExists('product_variations');
    }
}
