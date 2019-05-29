<?php

if (! siteconf()->get('catalog.useOwnAdminRoutes')) {
    Route::group([
        'namespace' => 'PortedCheese\Catalog\Http\Controllers\Admin',
        'middleware' => ['web', 'role:admin|editor'],
        'as' => 'admin.',
        'prefix' => 'admin',
    ], function () {
        if (siteconf()->get('catalog.useCart')) {
            Route::group([
                'as' => 'cart.',
                'prefix' => 'cart',
            ], function () {

            });
        }

        // Статусы заказа.
        Route::resource('order-state', 'OrderStateController')->parameters([
            'order-state' => 'state',
        ]);

        // Заказы.
        Route::resource('order', 'OrderController')->except([
            'create', 'store', 'edit'
        ]);

        // Метки товара.
        Route::resource('product-state', 'ProductStateController')->parameters([
            'product-state' => 'state',
        ]);

        // Управление категориями.
        Route::resource('category', 'CategoryController');

        // Все товары.
        Route::get('product', 'ProductController@index')
            ->name('product.index');

        // Категории.
        Route::group([
            'prefix' => 'category/{category}',
            'as' => 'category.'
        ], function () {
            // Мета.
            Route::get('metas', 'CategoryController@metas')
                ->name('metas');

            // Добавить подкатегорию.
            Route::get('create-child', 'CategoryController@create')
                ->name('create-child');

            // Удалить изображение.
            Route::delete('delete-image', 'CategoryController@destroyImage')
                ->name('destroy-image');

            // Изменить родителя.
            Route::put('change-parent', 'CategoryController@changeParent')
                ->name('change-parent');

            // Изменить вес.
            Route::put('change-weight', 'CategoryController@changeWeight')
                ->name('change-weight');

            // Поля категории.
            Route::resource('field', 'CategoryFieldController')->except([
                'show'
            ]);

            // Синхронизация полей.
            Route::post('field/sync', 'CategoryFieldController@syncChildren')
                ->name('field.sync');

            // Товары категории.
            Route::resource('product', 'ProductController');

            Route::group([
                'prefix' => 'product/{product}',
                'as' => 'product.',
            ], function () {
                // Мета.
                Route::get('metas', 'ProductController@metas')
                    ->name('metas');

                // Характеристики.
                Route::resource('field', 'ProductFieldController')->except([
                    'show'
                ]);

                // Удалить изображение.
                Route::delete('delete-image', 'ProductController@destroyImage')
                    ->name('destroy-image');

                // Сменить статус публикации.
                Route::put('published', 'ProductController@published')
                    ->name('published');

                // Галлерея.
                Route::get('gallery', 'ProductController@gallery')
                    ->name('gallery');

                // Вариации товара.
                Route::resource('variation', 'ProductVariationController')->except([
                    'show'
                ]);
            });
        });
    });
}

if (! siteconf()->get('catalog.useOwnSiteRoutes')) {
    Route::group([
        'namespace' => 'PortedCheese\Catalog\Http\Controllers\Site',
        'middleware' => ['web'],
        'as' => 'site.',
    ], function () {
        if (siteconf()->get('catalog.useCart')) {
            Route::group([
                'as' => 'cart.',
                'prefix' => 'cart',
            ], function () {
                Route::get('/', "CartController@index")
                    ->name('index');
                Route::put('/add/{product}', "CartController@addToCart")
                    ->name('add');
                Route::delete('/delete/{product}/{variation}', "CartController@deleteFromCart")
                    ->name('delete');
                Route::put("/change/{product}/{variation}", "CartController@changeQuantity")
                    ->name('change-quantity');
                Route::get('/checkout', "CartController@checkout")
                    ->name('checkout');
                Route::post("/order", "OrderController@makeCartOrder")
                    ->name('order');
            });
        }
        Route::group([
            'as' => 'catalog.',
            'prefix' => 'catalog',
        ], function () {
            // Категории товара.
            Route::get('/', 'CatalogController@index')
                ->name('index');
            Route::get('/{category}', 'CatalogController@showCategory')
                ->name('category.show');

            // Просмотр товара.
            Route::get('/{category}/{product}', 'CatalogController@showProduct')
                ->name('product.show');

            // Заказ одного товара.
            Route::post('/{product}/make-order', 'OrderController@makeProductOrder')
                ->name('order-product');
        });
    });
}