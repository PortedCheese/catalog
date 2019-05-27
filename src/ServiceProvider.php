<?php

namespace PortedCheese\Catalog;

use App\Category;
use App\Product;
use PortedCheese\Catalog\Console\Commands\CatalogMakeCommand;
use PortedCheese\Catalog\Events\CategoryFieldUpdate;
use PortedCheese\Catalog\Events\CreateNewOrder;
use PortedCheese\Catalog\Events\ProductFieldUpdate;
use PortedCheese\Catalog\Listeners\CategoryFieldClearCache;
use PortedCheese\Catalog\Listeners\ProductFieldClearCache;
use PortedCheese\Catalog\Listeners\SendNewOrderNotify;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
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

        // Подключение миграций.
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Подключение роутов.
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Подгрузка шаблонов.
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'catalog');

        // Console.
        if ($this->app->runningInConsole()) {
            $this->commands([
                CatalogMakeCommand::class,
            ]);
        }

        // Подписаться на обновление полей.
        $this->app['events']->listen(CategoryFieldUpdate::class, CategoryFieldClearCache::class);
        $this->app['events']->listen(ProductFieldUpdate::class, ProductFieldClearCache::class);
        // Создание заказа.
        $this->app['events']->listen(CreateNewOrder::class, SendNewOrderNotify::class);
    }

    public function register()
    {

    }

}
