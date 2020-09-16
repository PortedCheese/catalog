<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePricesOnProductVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("product_variations", function (Blueprint $table) {
            $table->decimal("buffer_price")
                ->nullable();
            $table->decimal("buffer_sale_price")
                ->nullable();
        });

        if (class_exists(\App\ProductVariation::class)) {
            foreach (\App\ProductVariation::all() as $item) {
                $item->buffer_price = $item->price;
                $item->buffer_sale_price = $item->sale_price;
                $item->save();
            }
        }

        Schema::table("product_variations", function (Blueprint $table) {
            $table->dropColumn("price", "sale_price");
        });

        Schema::table("product_variations", function (Blueprint $table) {
            $table->decimal("price")
                ->nullable()
                ->after("sku")
                ->comment("Цена");

            $table->decimal("sale_price")
                ->nullable()
                ->after("price")
                ->comment("Цена со скидкой");
        });

        if (class_exists(\App\ProductVariation::class)) {
            foreach (\App\ProductVariation::all() as $item) {
                $item->price = $item->buffer_price;
                $item->sale_price = $item->buffer_sale_price;
                $item->save();
            }
        }

        Schema::table("product_variations", function (Blueprint $table) {
            $table->dropColumn("buffer_price", "buffer_sale_price");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("product_variations", function (Blueprint $table) {
            $table->decimal("buffer_price")
                ->nullable();
            $table->decimal("buffer_sale_price")
                ->nullable();
        });

        if (class_exists(\App\ProductVariation::class)) {
            foreach (\App\ProductVariation::all() as $item) {
                $item->buffer_price = $item->price;
                $item->buffer_sale_price = $item->sale_price;
                $item->save();
            }
        }

        Schema::table("product_variations", function (Blueprint $table) {
            $table->dropColumn("price", "sale_price");
        });

        Schema::table("product_variations", function (Blueprint $table) {
            $table->float('price')
                ->after("sku")
                ->comment("Цена");

            $table->float('sale_price')
                ->nullable()
                ->after("price")
                ->comment("Цена со скидкой");
        });

        if (class_exists(\App\ProductVariation::class)) {
            foreach (\App\ProductVariation::all() as $item) {
                $item->price = $item->buffer_price;
                $item->sale_price = $item->buffer_sale_price;
                $item->save();
            }
        }

        Schema::table("product_variations", function (Blueprint $table) {
            $table->dropColumn("buffer_price", "buffer_sale_price");
        });
    }
}
