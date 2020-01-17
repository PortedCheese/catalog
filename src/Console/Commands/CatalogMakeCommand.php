<?php

namespace PortedCheese\Catalog\Console\Commands;

use App\Menu;
use App\MenuItem;
use PortedCheese\BaseSettings\Console\Commands\BaseConfigModelCommand;

class CatalogMakeCommand extends BaseConfigModelCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:catalog
                    {--all : Run all}
                    {--menu : Config menu}
                    {--models : Export models}
                    {--controllers : Export controllers}
                    {--vue : Export vue}
                    {--config : Make config}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Settings for catalog';

    /**
     * Имя пакета.
     *
     * @var string
     */
    protected $packageName = "Catalog";

    /**
     * The models that need to be exported.
     * @var array
     */
    protected $models = [
        'Category', 'CategoryField', 'CategoryFieldGroup',
        'Product', 'ProductState', 'ProductVariation', 'ProductField',
        'Order', 'OrderState', 'OrderItem', 'Cart',
    ];

    /**
     * Создание контроллеров.
     *
     * @var array
     */
    protected $controllers = [
        "Admin" => [
            "CartController", "CategoryController", "CategoryFieldController", "CategoryFieldGroupController",
            "OrderController", "OrderStateController",
            "ProductController", "ProductFieldController", "ProductStateController", "ProductVariationController",
        ],
        "Site" => [
            "CartController", "CatalogController", "OrderController",
        ],
    ];

    /**
     * Имя конфига.
     *
     * @var string
     */
    protected $configName = "catalog";

    /**
     * Заголовок конфига.
     *
     * @var string
     */
    protected $configTitle = "Каталог";

    /**
     * Шаблон настроек.
     *
     * @var string
     */
    protected $configTemplate = "catalog::admin.settings";

    /**
     * Значения конфига.
     *
     * @var array
     */
    protected $configValues = [
        'useCart' => false,
        "disablePriceSort" => true,
        "oldCardLive" => 7,
        "cartsAdminPager" => 10,
        "ordersAdminPager" => 20,
        "productsAdminPager" => 20,
        "productStatesAdminPager" => 20,
        "ordersProfilePager" => 10,
        "productsSitePager" => 18,
        "hasExchange" => false,
        "orderNotificationEmail" => "dev@gis4biz.ru",
    ];

    /**
     * Папка для vue файлов.
     *
     * @var string
     */
    protected $vueFolder = "catalog";

    /**
     * Список vue файлов.
     *
     * @var array
     */
    protected $vueIncludes = [
        'admin' => [
            'cart-state' => "CartStateComponent",
            'change-item-quantity' => "ChangeItemQuantityComponent",
        ],
        'app' => [
            "catalog-single-order" => "SingleProductComponent",
            "add-to-cart" => "AddToCardComponent",
            "cart-state" => "CartStateComponent",
            "change-item-quantity" => "ChangeItemQuantityComponent",
        ],
    ];

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
     */
    public function handle()
    {
        $all = $this->option("all");
        
        if ($this->option('menu') || $all) {
            $this->makeMenu();
        }
        
        if ($this->option("models") || $all) {
            $this->exportModels();
        }
        
        if ($this->option("controllers") || $all) {
            $this->exportControllers("Admin");
            $this->exportControllers("Site");
        }
        
        if ($this->option("vue") || $all) {
            $this->makeVueIncludes("admin");
            $this->makeVueIncludes("app");
        }
        
        if ($this->option("config") || $all) {
            $this->makeConfig();
        }
    }

    /**
     * Заполнить меню.
     */
    protected function makeMenu() {
        try {
            $menu = Menu::query()->where('key', 'admin')->firstOrFail();
        }
        catch (\Exception $e) {
            return;
        }

        $title = "Каталог";
        $itemData = [
            "title" => $title,
            "menu_id" => $menu->id,
            "url" => "#",
            "template" => "catalog::admin.menu"
        ];

        try {
            $menuItem = MenuItem::query()
                ->where("menu_id", $menu->id)
                ->where('title', $title)
                ->firstOrFail();
            $menuItem->update($itemData);
            $this->info("Элемент меню '$title' обновлен");
        }
        catch (\Exception $e) {
            MenuItem::create($itemData);
            $this->info("Элемент меню '$title' создан");
        }

//        $this->makeCategoryMenu($menu);
//        $this->makeOrderMenu($menu);
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
            'active' => [
                "admin.order-state.*",
                "admin.order.*",
                "admin.cart.*",
            ],
            'ico' =>'fab fa-jedi-order',
            'menu_id' => $menu->id,
            'url' => '#',
        ];
        $menuItem = $this->updateOrCreateItem($itemData);

        $title = "Статусы";
        $this->updateOrCreateItem([
            'title' => $title,
            'menu_id' => $menu->id,
            'parent_id' => $menuItem->id,
            'route' => 'admin.order-state.index',
        ]);

        $title = "Список";
        $this->updateOrCreateItem([
            'title' => $title,
            'menu_id' => $menu->id,
            'parent_id' => $menuItem->id,
            'route' => 'admin.order.index',
        ]);

        $title = "Корзины";
        if (siteconf()->get("catalog", "useCart")) {
            $this->updateOrCreateItem([
                'title' => $title,
                'menu_id' => $menu->id,
                'parent_id' => $menuItem->id,
                'route' => 'admin.cart.index',
            ]);
        }
        else {
            try {
                $menu = MenuItem::where('route', "admin.cart.index")->firstOrFail();
                $menu->delete();
            }
            catch (\Exception $exception) {
                $this->error("Невозможно удалить $title");
            }
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
            'active' => [
                "admin.category.*",
                "admin.category.field.*",
                "admin.category.product.*",
                "admin.product.*",
                "admin.category.product.field.*",
                "admin.category.product.variation.*",
                "admin.product-state.*",
                "admin.category.all-fields.*",
                "admin.category.groups.*",
            ],
            'ico' =>'fas fa-stream',
            'menu_id' => $menu->id,
            'url' => '#',
        ];
        $menuItem = $this->updateOrCreateItem($itemData);

        $title = "Список";
        $this->updateOrCreateItem([
            'title' => $title,
            'menu_id' => $menu->id,
            'parent_id' => $menuItem->id,
            'route' => 'admin.category.index',
        ]);

        $title = 'Создать';
        $this->updateOrCreateItem([
            'title' => $title,
            'menu_id' => $menu->id,
            'parent_id' => $menuItem->id,
            'route' => 'admin.category.create',
        ]);

        $title = 'Товары';
        $this->updateOrCreateItem([
            'title' => $title,
            'menu_id' => $menu->id,
            'parent_id' => $menuItem->id,
            'route' => 'admin.product.index',
        ]);

        $title = 'Метки товара';
        $this->updateOrCreateItem([
            'title' => $title,
            'menu_id' => $menu->id,
            'parent_id' => $menuItem->id,
            'route' => 'admin.product-state.index',
        ]);

        $title = "Характеристики";
        $this->updateOrCreateItem([
            'title' => $title,
            'menu_id' => $menu->id,
            'parent_id' => $menuItem->id,
            'route' => 'admin.category.all-fields.list',
        ]);

        $title = "Группы";
        $this->updateOrCreateItem([
            'title' => $title,
            'menu_id' => $menu->id,
            'parent_id' => $menuItem->id,
            'route' => "admin.category.groups.index",
        ]);
    }

    /**
     * Обновить или создать элемен меню.
     *
     * @param $itemData
     * @return mixed
     */
    private function updateOrCreateItem($itemData)
    {
        $title = $itemData['title'];
        try {
            $query = MenuItem::query()
                ->where('title', $itemData['title'])
                ->where("menu_id", $itemData['menu_id']);
            if (! empty($itemData['parent_id'])) {
                $query->where("parent_id", $itemData['parent_id']);
            }
            $menuItem = $query->firstOrFail();
            $menuItem->update($itemData);
            $this->info("Элемент меню '$title' обновлен");
        }
        catch (\Exception $e) {
            $menuItem = MenuItem::create($itemData);
            $this->info("Элемент меню '$title' создан");
        }
        return $menuItem;
    }
}
