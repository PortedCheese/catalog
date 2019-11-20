<?php

namespace PortedCheese\Catalog;

use App\Cart;
use App\Category;
use App\Product;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use PortedCheese\Catalog\Console\Commands\CatalogClearCardsCommand;
use PortedCheese\Catalog\Console\Commands\CatalogMakeCommand;
use PortedCheese\Catalog\Console\Kernel;
use PortedCheese\Catalog\Events\CategoryFieldUpdate;
use PortedCheese\Catalog\Events\CreateNewOrder;
use PortedCheese\Catalog\Events\ProductCategoryChange;
use PortedCheese\Catalog\Events\ProductFieldUpdate;
use PortedCheese\Catalog\Events\ProductListChange;
use PortedCheese\Catalog\Events\ProductVariationUpdate;
use PortedCheese\Catalog\Listeners\AddFieldsToProductCategory;
use PortedCheese\Catalog\Listeners\CategoryFieldClearCache;
use PortedCheese\Catalog\Listeners\ProductFieldClearCache;
use PortedCheese\Catalog\Listeners\ProductFilterClearCache;
use PortedCheese\Catalog\Listeners\ProductTeaserClearCache;
use PortedCheese\Catalog\Listeners\ProductValuesFilterClearCache;
use PortedCheese\Catalog\Listeners\ProductVariationsFilterClearCache;
use PortedCheese\Catalog\Listeners\SendNewOrderNotify;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        $this->makeConfigVariables();

        // Подключение миграций.
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Подключение роутов.
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/admin.php');

        // Подгрузка шаблонов.
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'catalog');

        // Assets.
        $this->publishes([
            __DIR__ . '/resources/js/components' => resource_path('js/components/vendor/catalog'),
        ], 'public');

        // Console.
        if ($this->app->runningInConsole()) {
            $this->commands([
                CatalogMakeCommand::class,
                CatalogClearCardsCommand::class,
            ]);
        }

        $this->makeEvents();
        $this->makeViewsVariables();
    }

    public function register()
    {
        if (class_exists(Cart::class) && Cart::CRON_ENABLED) {
            $this->app->singleton('portedcheese.catalog.console.kernel', function ($app) {
                $dispatcher = $app->make(Dispatcher::class);
                return new Kernel($app, $dispatcher);
            });

            $this->app->make('portedcheese.catalog.console.kernel');
        }
    }

    /**
     * Расширить текущий конфиг.
     */
    private function makeConfigVariables()
    {
        // Подключаем метатеги.
        $seo = app()->config['seo-integration.models'];
        $seo['categories'] = Category::class;
        $seo['products'] = Product::class;
        app()->config['seo-integration.models'] = $seo;

        // Подключаем изображения.
        $imagecache = app()->config['imagecache.paths'];
        $imagecache[] = 'storage/categories';
        $imagecache[] = 'storage/products';
        $imagecache[] = 'storage/gallery/products';
        app()->config['imagecache.paths'] = $imagecache;

        // Подключаем галлерею.
        $gallery = app()->config['gallery.models'];
        $gallery['products'] = Product::class;
        app()->config['gallery.models'] = $gallery;
    }

    /**
     * Передаем переменные в представления.
     */
    private function makeViewsVariables()
    {
        // Информация о текущей корзине.
        view()->composer('catalog::site.cart.cart-state', function ($view) {
            $cartData = (object) [
                'total' => 0,
                'count' => 0,
            ];
            $cart = Cart::getCart();
            if ($cart) {
                $cartData->total = $cart->total;
                $cartData->count = $cart->getCount();
            }
            $view->with('cartData', $cartData);
        });
        // Фильтрация.
        view()->composer("catalog::site.products.filters", function ($view) {
            $request = app(Request::class);
            $queryParams = $request->query->all();
            $view->with("sortField", !empty($queryParams['sort-by']) ? $queryParams['sort-by'] : Product::DEFAULT_SORT);
            $view->with("sortOrder", !empty($queryParams['sort-order']) ? $queryParams['sort-order'] : Product::DEFAULT_SORT_ORDER);
        });
        // Сортировка.
        view()->composer("catalog::site.categories.sort", function ($view) {
            $view->with("disablePriceSort", siteconf()->get("catalog", "disablePriceSort"));
        });
        view()->composer("catalog::site.categories.sort-link", function ($view) {
            $request = app(Request::class);
            $queryParams = $request->query->all();

            if (!empty($queryParams['sort-by'])) {
                $field = $queryParams['sort-by'];
                unset($queryParams['sort-by']);
            }
            else {
                $field = Product::DEFAULT_SORT;
            }
            $view->with("sortField", $field);

            if (!empty($queryParams['sort-order'])) {
                $order = $queryParams['sort-order'];
                unset($queryParams['sort-order']);
            }
            else {
                $order = Product::DEFAULT_SORT_ORDER;
            }
            $view->with("sortOrder", $order);

            $route = Route::current();
            $routeName = Route::currentRouteName();
            $routeParams = $route->parameters();
            foreach ($queryParams as $key => $value) {
                $routeParams[$key] = $value;
            }
            $uri = route($routeName, $routeParams);
            $view->with("noParams", empty($queryParams));
            $view->with("sortUrl", $uri);
        });

        Blade::include("catalog::site.categories.sort-link", "sortLink");
    }

    /**
     * Подписаться на события.
     */
    private function makeEvents()
    {
        // Подписаться на обновление полей.
        $this->app['events']->listen(CategoryFieldUpdate::class, CategoryFieldClearCache::class);
        $this->app['events']->listen(ProductFieldUpdate::class, ProductFieldClearCache::class);
        // Создание заказа.
        $this->app['events']->listen(CreateNewOrder::class, SendNewOrderNotify::class);
        // Обновление вариации.
        $this->app['events']->listen(ProductVariationUpdate::class, ProductTeaserClearCache::class);
        // Изменение фильтров.
        $this->app['events']->listen(ProductListChange::class, ProductFilterClearCache::class);
        $this->app['events']->listen(ProductFieldUpdate::class, ProductValuesFilterClearCache::class);
        $this->app['events']->listen(ProductVariationUpdate::class, ProductVariationsFilterClearCache::class);
        // Изменение категории.
        $this->app['events']->listen(ProductCategoryChange::class, AddFieldsToProductCategory::class);
    }

}
