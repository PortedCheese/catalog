<?php

namespace PortedCheese\Catalog\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use PortedCheese\Catalog\Events\CategoryFieldUpdate;

class CategoryFieldClearCache
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
    public function handle(CategoryFieldUpdate $event)
    {
        $category = $event->category;
        // Очистка кэша.
        $category->forgetFieldsCache();
        $category->forgetChildrenFieldsCache();
    }
}
