<?php

if (! siteconf()->get('catalog.useOwnAdminRoutes')) {
    Route::group([
        'namespace' => 'PortedCheese\Catalog\Http\Controllers\Admin',
        'middleware' => ['web', 'role:admin|editor'],
        'as' => 'admin.',
        'prefix' => 'admin',
    ], function () {
        // Управление категориями.
        Route::resource('category', 'CategoryController');

        // Все товары.
        Route::get('product', 'ProductController@index')
            ->name('product.index');

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