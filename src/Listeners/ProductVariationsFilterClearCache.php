<?php

namespace PortedCheese\Catalog\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use PortedCheese\Catalog\Events\ProductVariationUpdate;

class ProductVariationsFilterClearCache
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
     * @param  object  $event
     * @return void
     */
    public function handle(ProductVariationUpdate $event)
    {
        $product = $event->product;
        if (! empty($product)) {
            $category = $product->category;
            if (! empty($category)) {
                $category->forgetFilterVariationsCache();
            }
        }
    }
}
