<?php

namespace PortedCheese\Catalog\Events;

use App\Product;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ProductListChange
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $categoryId;

    /**
     * Create a new event instance.
     *
     * @param Product $product
     * @param $categoryId
     * @return void
     */
    public function __construct(Product $product, $categoryId = false)
    {
        $this->product = $product;
        $this->categoryId = $categoryId;
    }
}
