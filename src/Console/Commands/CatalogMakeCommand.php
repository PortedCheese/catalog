<?php

namespace PortedCheese\Catalog\Console\Commands;

use App\Menu;
use App\MenuItem;
use Illuminate\Console\DetectsApplicationNamespace;
use PortedCheese\BaseSettings\Console\Commands\BaseConfigModelCommand;

class CatalogMakeCommand extends BaseConfigModelCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:catalog
                    {--menu : Only config menu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Settings for catalog';

    /**
     * The models that need to be exported.
     * @var array
     */
    protected $models = [
        'Category.stub' => 'Category.php',
        'CategoryField.stub' => 'CategoryField.php',
        'Product.stub' => 'Product.php',
        'ProductState.stub' => 'ProductState.php',
        'ProductVariation.stub' => 'ProductVariation.php',
        'ProductField.stub' => 'ProductField.php',
        'Order.stub' => 'Order.php',
        'OrderState.stub' => 'OrderState.php',
        'OrderItem.stub' => 'OrderItem.php',
        'Cart.stub' => 'Cart.php',
    ];

    protected $controllers = [
        "Admin" => [
            "CartController",
            "CategoryController",
            "CategoryFieldController",
            "OrderController",
            "OrderStateController",
            "ProductController",
            "ProductFieldController",
            "ProductStateController",
            "ProductVariationController",
        ],
        "Site" => [
            "CartController",
            "CatalogController",
            "OrderController",
        ],
    ];
    protected $packageName = "Catalog";

    /**
     * Имя конфига.
     *
     * @var string
     */
    protected $configName = 'catalog';

    /**
     * Значения конфигов.
     *
     * @var array
     */
    protected $configValues = [
        'useOwnSiteRoutes' => false,
        'useOwnAdminRoutes' => false,
        'useCart' => false,
    ];

    /**
     * Директория пакета.
     *
     * @var string
     */
    protected $dir = __DIR__;

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
        if (! $this->option('menu')) {
            $this->exportModels();
            $this->exportControllers("Admin");
            $this->exportControllers("Site");
        }
        $this->makeMenu();
        $this->makeConfig();
    }

    /**
     * Заполнить меню.
     */
    protected function makeMenu() {
        try {
            $menu = Menu::where('key', 'admin')->firstOrFail();
        }
        catch (\Exception $e) {
            return;
        }

        $this->makeCategoryMenu($menu);
        $this->makeOrderMenu($menu);
    }

    /**
     * Меню заказа.
     *
     * @param $menu
     */
    private function makeOrderMenu($menu)
    {
        $title = "Заказы";
        $itemData = [
            'title' => $title,
            'route' => '@admin.order-state.*|admin.order.*|admin.cart.*',
            'class' =>'@fab fa-jedi-order',
            'menu_id' => $menu->id,
            'url' => '#',
        ];
        try {
            $menuItem = MenuItem::where('title', $title)->firstOrFail();
            $menuItem->update($itemData);
            $this->info("Элемент меню '$title' обновлен");
        }
        catch (\Exception $e) {
            $menuItem = MenuItem::create($itemData);
            $this->info("Элемент меню '$title' создан");
        }
        $children = $menuItem->children;
        $titles = [];
        foreach ($children as $child) {
            $titles[] = $child->title;
        }

        $title = "Статусы";
        if (! in_array($title, $titles)) {
            MenuItem::create([
                'title' => $title,
                'menu_id' => $menu->id,
                'parent_id' => $menuItem->id,
                'route' => 'admin.order-state.index',
            ]);
            $this->info("Элемент меню 'Заказы.{$title}' создан");
        }

        $title = "Список";
        if (! in_array($title, $titles)) {
            MenuItem::create([
                'title' => $title,
                'menu_id' => $menu->id,
                'parent_id' => $menuItem->id,
                'route' => 'admin.order.index',
            ]);
            $this->info("Элемент меню 'Заказы.{$title}' создан");
        }

        $title = "Корзины";
        if (! in_array($title, $titles)) {
            MenuItem::create([
                'title' => $title,
                'menu_id' => $menu->id,
                'parent_id' => $menuItem->id,
                'route' => 'admin.cart.index',
            ]);
            $this->info("Элемент меню 'Заказы.{$title}' создан");
        }
    }

    /**
     * Меню категории.
     *
     * @param $menu
     */
    private function makeCategoryMenu($menu)
    {
        $title = "Категории";
        $itemData = [
            'title' => $title,
            'route' => '@admin.category.*|admin.category.field.*|admin.category.product.*|admin.product.*|admin.category.product.field.*|admin.category.product.variation.*|admin.product-state.*',
            'class' =>'@fas fa-stream',
            'menu_id' => $menu->id,
            'url' => '#',
        ];
        try {
            $menuItem = MenuItem::where('title', $title)->firstOrFail();
            $menuItem->update($itemData);
            $this->info("Элемент меню '$title' обновлен");
        }
        catch (\Exception $e) {
            $menuItem = MenuItem::create($itemData);
            $this->info("Элемент меню '$title' создан");
        }
        $children = $menuItem->children;
        $titles = [];
        foreach ($children as $child) {
            $titles[] = $child->title;
        }

        $title = "Список";
        if (! in_array($title, $titles)) {
            MenuItem::create([
                'title' => $title,
                'menu_id' => $menu->id,
                'parent_id' => $menuItem->id,
                'route' => 'admin.category.index',
            ]);
            $this->info("Элемент меню 'Категории.{$title}' создан");
        }

        $title = 'Создать';
        if (! in_array($title, $titles)) {
            MenuItem::create([
                'title' => $title,
                'menu_id' => $menu->id,
                'parent_id' => $menuItem->id,
                'route' => 'admin.category.create',
            ]);
            $this->info("Элемент меню 'Категории.{$title}' создан");
        }

        $title = 'Товары';
        if (! in_array($title, $titles)) {
            MenuItem::create([
                'title' => $title,
                'menu_id' => $menu->id,
                'parent_id' => $menuItem->id,
                'route' => 'admin.product.index',
            ]);
            $this->info("Элемент меню 'Категории.{$title}' создан");
        }

        $title = 'Метки товара';
        if (! in_array($title, $titles)) {
            MenuItem::create([
                'title' => $title,
                'menu_id' => $menu->id,
                'parent_id' => $menuItem->id,
                'route' => 'admin.product-state.index',
            ]);
            $this->info("Элемент меню 'Категории.{$title}' создан");
        }
    }
}
