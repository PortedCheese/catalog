<?php

namespace PortedCheese\Catalog\Console;

use App\Cart;
use App\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use PortedCheese\Catalog\Console\Commands\CatalogClearCardsCommand;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        parent::schedule($schedule);

        $schedule
            ->command(CatalogClearCardsCommand::class)
            ->hourly();
    }
}