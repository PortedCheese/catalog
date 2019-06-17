<?php

namespace PortedCheese\Catalog\Events;

use App\Product;
use App\ProductVariation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProductVariationUpdate
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $variation;
    public $product;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProductVariation $variation)
    {
        $this->variation = $variation;

        $this->product = $variation->product;
    }
}
