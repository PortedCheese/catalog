# Catalog

## Description
Каталог товаров разбитых на категории. По умолчанию на каждый товар можно отправить заявку, через настройки можно включить корзину.

Есть возможность автоматически удалять старые корзины, для этого нужно включить крон.

## Install
    php artisan migrate

    php artisan vendor:publish --provider="PortedCheese\Catalog\ServiceProvider" --tag=public

    php artisan make:catalog {--all : Run all}
                             {--menu : Config menu}
                             {--models : Export models}
                             {--controllers : Export controllers}
                             {--vue : Export vue}
                             {--config : Make config}
Настройки каталога. Создает необходимые модели и контроллеры, методы которых можно переопределить, создает элементы меню и создает конфигурацию.

`@includeIf("catalog::site.cart.cart-state")` - Добавить элемент li в меню для корзины

## Settings
В модели Card есть константа CRON_ENABLED - Включить удаление устаревших корзин анонимов