<?php

namespace PortedCheese\Catalog\Listeners;

use PortedCheese\Catalog\Events\ProductFieldUpdate;

class ProductValuesFilterClearCache
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ProductFieldUpdate  $event
     * @return void
     */
    public function handle(ProductFieldUpdate $event)
    {
        $product = $event->product;
        $category = $product->category;
        if (! empty($category)) {
            $category->forgetProductValuesCache();
        }
    }
}
