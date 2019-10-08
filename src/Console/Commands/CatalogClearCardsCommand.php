<?php

namespace PortedCheese\Catalog\Console\Commands;

use App\Cart;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CatalogClearCardsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear old cards';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $days = siteconf()->get("catalog", "oldCardLive");
        $ts = strtotime("- $days days", time());
        $date = date("Y-m-d H:i:s", $ts);
        $carts = Cart::query()
            ->select("id")
            ->whereNull("user_id")
            ->where("updated_at", '<=', $date)
            ->limit(10)
            ->get();

        foreach ($carts as $cart) {
            $cart->delete();
        }
    }
}
