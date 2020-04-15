# Catalog

## Description
Каталог товаров разбитых на категории. По умолчанию на каждый товар можно отправить заявку, через настройки можно включить корзину.

Есть возможность автоматически удалять старые корзины, для этого нужно включить крон.

## Install
    php artisan migrate

    php artisan vendor:publish --provider="PortedCheese\Catalog\ServiceProvider" --tag=public --force

    php artisan make:catalog
                            {--all : Run all}
                            {--menu : Config menu}
                            {--models : Export models}
                            {--controllers : Export controllers}
                            {--policies : Export and create rules}
                            {--only-default : Create default rules}
                            {--vue : Export vue}
                            {--config : Make config}
Настройки каталога. Создает необходимые модели и контроллеры, методы которых можно переопределить, создает элементы меню и создает конфигурацию.

`@includeIf("catalog::site.cart.cart-state")` - Добавить элемент li в меню для корзины

## Settings
В модели Card есть константа CRON_ENABLED - Включить удаление устаревших корзин анонимов

### Versions
    
    v1.1.19:
        - В классе Cart вызовы self заменены на App\Cart
        
    v1.1.16:
        - Машинное имя у характеристик скрыто для обычного пользователя.
        - Теперь можно поменять виджет поля характеристики.
        - Изменен интерфейс для групп, убрано машинное имя для обычного пользователя.
        - При выводе категорий добавлена сортировка.
    
    v1.1.15:
        - Исправлен вывод изображений товара
        - Добавлена форма поиска у характеристик и исправлена пагинация
    
    v1.1.14:
        - Сброс кэша тизеров товара при обновлении slug у категории
        - Изменено кэширование тизера товара, теперь запоминаются только данные не шаблон
        - При выводе полго товара вариации формируются в модели
        - Если вариации одна, то радио кнопки скрыты
    Обновление:
        - php artisan cache:clear
        - php artisan vendor:publish --provider="PortedCheese\Catalog\ServiceProvider" --tag=public --force
    
    v1.1.13:
        - Изменен вывод изображение в админке
    
    v1.1.12:
        - Изменены поля в вариации(float на decimal)
        - Добвлен параметр в команду --only-default
    Обновление:
        - php artisan migrate
    
    v1.1.10:
        - Добавлены права доступа
    Обновление:
        - php artisan make:catalog --policies

    v1.1.9:
        - Изменено меню, перенесено в два шаблона
        - Структура каталога с vue draggable
        - Исправлено изменение категории товара
        - Изменена валидация добавления категории
    Обновление:
        - Удалить старые пункты меню
        - php artisan vendor:publish --provider="PortedCheese\Catalog\ServiceProvider" --tag=public
        - php artisan make:catalog --menu --vue
