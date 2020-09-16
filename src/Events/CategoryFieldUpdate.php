<?php

namespace PortedCheese\Catalog\Events;

use App\Category;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class CategoryFieldUpdate
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $category;

    /**
     * Create a new event instance.
     *
     * CategoryFieldUpdate constructor.
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }
}
