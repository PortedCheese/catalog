<?php

namespace PortedCheese\Catalog\Events;

use App\Product;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ProductCategoryChange
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $categoryId;

    /**
     * Create a new event instance.
     *
     * @param Product $product
     * @param integer $categoryId
     * @return void
     */
    public function __construct(Product $product, int $categoryId)
    {
        $this->product = $product;
        $this->categoryId = $categoryId;
    }
}
