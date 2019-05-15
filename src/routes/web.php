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
        Route::group([
            'prefix' => 'category',
            'as' => 'category.'
        ], function () {
            // Мета.
            Route::get('{category}/metas', 'CategoryController@metas')
                ->name('metas');
            // Добавить подкатегорию.
            Route::get('{category}/create-child', 'CategoryController@create')
                ->name('create-child');
            // Удалить изображение.
            Route::delete('{category}/delete-image', 'CategoryController@destroyImage')
                ->name('destroy-image');
            // Изменить родителя.
            Route::put('{category}/change-parent', 'CategoryController@changeParent')
                ->name('change-parent');
            // Изменить вес.
            Route::put('{category}/change-weight', 'CategoryController@changeWeight')
                ->name('change-weight');
            // Поля категории.
            Route::group([
                'prefix' => '{category}',
            ], function () {
                Route::resource('field', 'CategoryFieldController')->except([
                    'show'
                ]);
                Route::group([
                    'prefix' => 'field',
                    'as' => 'field.',
                ], function () {
                    Route::post('sync', 'CategoryFieldController@syncChildren')
                        ->name('sync');
                });
            });
        });
    });
}