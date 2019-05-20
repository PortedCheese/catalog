<?php

namespace PortedCheese\Catalog\Console\Commands;

use App\Menu;
use App\MenuItem;
use Illuminate\Console\DetectsApplicationNamespace;
use PortedCheese\BaseSettings\Console\Commands\BaseConfigModelCommand;

class CatalogMakeCommand extends BaseConfigModelCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:catalog
                    {--menu : Only config menu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Settings for catalog';

    /**
     * The models that need to be exported.
     * @var array
     */
    protected $models = [
        'Category.stub' => 'Category.php',
        'CategoryField.stub' => 'CategoryField.php',
        'Product.stub' => 'Product.php',
        'ProductVariation.stub' => 'ProductVariation.php',
        'ProductField.stub' => 'ProductField.php',
    ];

    protected $dir = __DIR__;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->option('menu')) {
            $this->exportModels();
        }
        $this->makeMenu();
    }

    protected function makeMenu() {
        try {
            $menu = Menu::where('key', 'admin')->firstOrFail();
        }
        catch (\Exception $e) {
            return;
        }
        $title = "Категории";
        $itemData = [
            'title' => $title,
            'route' => '@admin.category.*|admin.category.field.*|admin.category.product.*|admin.product.*|admin.category.product.field.*|admin.category.product.variation.*',
            'class' =>'@fas fa-stream',
            'menu_id' => $menu->id,
        ];
        try {
            $menuItem = MenuItem::where('title', $title)->firstOrFail();
            $menuItem->update($itemData);
            $this->info("Элемент меню '$title' обновлен");
        }
        catch (\Exception $e) {
            $menuItem = MenuItem::create($itemData);
            $this->info("Элемент меню '$title' создан");
        }
        $children = $menuItem->children;
        $titles = [];
        foreach ($children as $child) {
            $titles[] = $child->title;
        }

        if (! in_array('Список', $titles)) {
            MenuItem::create([
                'title' => "Список",
                'menu_id' => $menu->id,
                'parent_id' => $menuItem->id,
                'route' => 'admin.category.index',
            ]);
            $this->info("Элемент меню '{$title}.Список' создан");
        }

        if (! in_array('Создать', $titles)) {
            MenuItem::create([
                'title' => 'Создать',
                'menu_id' => $menu->id,
                'parent_id' => $menuItem->id,
                'route' => 'admin.category.create',
            ]);
            $this->info("Элемент меню '{$title}.Создать' создан");
        }

        if (! in_array('Товары', $titles)) {
            MenuItem::create([
                'title' => 'Товары',
                'menu_id' => $menu->id,
                'parent_id' => $menuItem->id,
                'route' => 'admin.product.index',
            ]);
            $this->info("Элемент меню '{$title}.Товары' создан");
        }
    }
}
