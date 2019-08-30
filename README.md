#Catalog

##Description
Каталог товаров разбитых на категории. По умолчанию на каждый товар можно отправить заявку, через настройки можно включить корзину.

Есть возможность автоматически удалять старые корзины, для этого нужно включить крон.

##Install
`php artisan migrate` - Создаст необходимые таблицы.

`php artisan make:catalog {--menu : Only config menu}` - Настройки каталога. Создает необходимые модели и контроллеры, методы которых можно переопределить, создает элементы меню и создает конфигурацию.

`php artisan vendor:publish --provider="PortedCheese\Catalog\ServiceProvider" --tag=public` - Копирует компоненты.

`Vue.component(
    'catalog-single-order',
    require('./components/vendor/catalog/SingleProductComponent')
);`

`Vue.component(
    'add-to-cart',
    require('./components/vendor/catalog/AddToCardComponent')
);`

`Vue.component(
    'cart-state',
    require('./components/vendor/catalog/CartStateComponent')
);`

`Vue.component(
    'change-item-quantity',
    require('./components/vendor/catalog/ChangeItemQuantityComponent')
);`

`@include("catalog::site.cart.cart-state")` - Добавить элемент li в меню для корзины

##Settings
###env
CATALOG_CRON=true - Включить удаление устаревших корзин анонимов
CATALOG_OLD_CARD_LIVE=7 - Сколько дней живут корзины анонимов

CATALOG_ORDERS_ADMIN_PAGER=20 - Сколько заказов на страницу в админке
CATALOG_PRODUCT_ADMIN_PAGER=20 - Сколько товаров на страницу в админке
CATALOG_PRODUCT_STATE_ADMIN_PAGER=20 - Сколько статусов товара на странице в админке

CATALOG_ORDERS_PROFILE_PAGER=10 - Сколько заказов в профиле пользователя

DISABLE_CATALOG_PRICE_SORT=false - Отключить сортировку по цене, это если в каталоге нет цен

CATALOG_ORDER_NOTIFY_EMAIL = "dev@gis4biz.ru" - куда отправлять уведомление о новом заказе.

CATALOG_HAS_EXCHANGE = false - если есть выгрузка выключить очередь на кэширование.