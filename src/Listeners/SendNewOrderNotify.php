<?php

namespace PortedCheese\Catalog\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNewOrderNotify
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
    public function handle($event)
    {
        $order = $event->order;

        if ($user = $order->user) {
            $user->notify($order->getNewOrderUserNotification());
        }

        $order->notify($order->getNewOrderClientNotification());
    }
}
