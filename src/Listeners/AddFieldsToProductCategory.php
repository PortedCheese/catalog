<?php

namespace PortedCheese\Catalog\Listeners;

use App\Category;
use App\Product;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use PortedCheese\Catalog\Events\ProductCategoryChange;

class AddFieldsToProductCategory
{

    /**
     * Текущий товар.
     *
     * @var null|Product
     */
    protected $product;
    /**
     * Текущая категория.
     *
     * @var null|Category
     */
    protected $category;
    /**
     * Исходная категрория.
     *
     * @var null|Category
     */
    protected $original;

    /**
     * AddFieldsToProductCategory constructor.
     */
    public function __construct()
    {
        $this->product = null;
        $this->category = null;
        $this->original = null;
    }

    /**
     * Handle the event.
     *
     * @param  ProductCategoryChange  $event
     * @return void
     */
    public function handle(ProductCategoryChange $event)
    {
        $this->product = $event->product;
        $original = $event->categoryId;
        try {
            $this->original = Category::findOrFail($original);
        }
        catch (\Exception $exception) {
            $this->original = null;
        }
        $this->addNewFields();
    }

    /**
     * Добавить новые поля в категорию.
     *
     * @return bool
     */
    private function addNewFields()
    {
        if (empty($this->original)) {
            return false;
        }
        if (! $this->setCategory()) {
            return false;
        }
        $this->changePivots();
        $this->category->setParentFields($this->original);

        $this->category->forgetFieldsCache();
        $this->category->forgetChildrenFieldsCache();

        $this->original->forgetFieldsCache();
        $this->original->forgetChildrenFieldsCache();
        return true;
    }

    /**
     * Изменить таблицу связку.
     */
    private function changePivots()
    {
        DB::table("product_fields")
            ->where("category_id", $this->original->id)
            ->where("product_id", $this->product->id)
            ->update([
                "category_id" => $this->category->id
            ]);
    }

    /**
     * Задать категорию.
     *
     * @return bool
     */
    private function setCategory()
    {
        if (empty($this->product)) {
            return false;
        }
        $this->category = $this->product->category;
        if (empty($this->category)) {
            return false;
        }
        return true;
    }
}
