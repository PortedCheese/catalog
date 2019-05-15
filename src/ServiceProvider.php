<?php

namespace PortedCheese\Catalog;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        // Подключаем метатеги.
        $seo = app()->config['seo-integration.models'];
        $seo['categories'] = 'PortedCheese\Catalog\Models\Category';
        app()->config['seo-integration.models'] = $seo;

        // Подключаем изображения.
        $imagecache = app()->config['imagecache.paths'];
        $imagecache[] = 'storage/categories';
        app()->config['imagecache.paths'] = $imagecache;

        // Подключение миграций.
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Подключение роутов.
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Подгрузка шаблонов.
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'catalog');
    }

    public function register()
    {

    }

}
