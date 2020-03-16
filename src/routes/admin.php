<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'App\Http\Controllers\Vendor\Catalog\Admin',
    'middleware' => ['web', 'management'],
    'as' => 'admin.',
    'prefix' => 'admin',
], function () {
    if (siteconf()->get("catalog", "useCart")) {
        Route::group([
            'as' => 'cart.',
            'prefix' => 'cart',
        ], function () {
            Route::get('/', 'CartController@index')
                ->name('index');
            Route::get('/{cart}', 'CartController@show')
                ->name('show');
            Route::delete('/{cart}', 'CartController@destroy')
                ->name('destroy');
        });
    }

    // Статусы заказа.
    Route::resource('order-state', 'OrderStateController')->parameters([
        'order-state' => 'state',
    ])->except([
        "show",
    ]);

    // Заказы.
    Route::resource('order', 'OrderController')->except([
        'create', 'store', 'edit'
    ]);

    // Метки товара.
    Route::resource('product-state', 'ProductStateController')->parameters([
        'product-state' => 'state',
    ]);

    // Все товары.
    Route::get('product', 'ProductController@index')
        ->name('product.index');

    // Доступные поля категори.
    Route::group([
        'prefix' => 'category-fields',
        'as' => "category.all-fields.",
    ], function () {
        Route::get('/', "CategoryFieldController@list")
            ->name('list');
        Route::get("/{field}", "CategoryFieldController@show")
            ->name("show");
        Route::put("/{field}", "CategoryFieldController@selfUpdate")
            ->name("self-update");
    });

    // Группы характеристик.
    Route::group([
        'as' => "category.",
    ], function () {
        Route::get("groups/priority", "CategoryFieldGroupController@priority")
            ->name("groups.priority");
        Route::resource("groups", "CategoryFieldGroupController")->except([
            'edit',
        ]);
    });

    // Управление категориями.
    Route::resource('category', 'CategoryController');
    Route::put("category", "CategoryController@changeItemsWeight")
        ->name("category.items-weight");

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
            // Сменить категорию.
            Route::put('change-category', "ProductController@changeCategory")
                ->name('change-category');
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