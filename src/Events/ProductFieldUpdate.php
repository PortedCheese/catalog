<?php

namespace PortedCheese\Catalog\Events;

use App\Product;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ProductFieldUpdate
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;

    /**
     * ProductFieldUpdate constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
