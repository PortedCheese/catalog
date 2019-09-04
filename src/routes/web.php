<?php

if (! siteconf()->get('catalog.useOwnSiteRoutes')) {
    Route::group([
        'namespace' => 'App\Http\Controllers\Vendor\Catalog\Site',
        'as' => 'profile.order.',
        'prefix' => 'profile/orders',
        'middleware' => ['web', 'auth', 'verified'],
    ], function () {
        Route::get('/', "OrderController@userList")
            ->name('index');
        Route::get("/{order}", "OrderController@showOrder")
            ->name('show');
    });

    Route::group([
        'namespace' => 'App\Http\Controllers\Vendor\Catalog\Site',
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