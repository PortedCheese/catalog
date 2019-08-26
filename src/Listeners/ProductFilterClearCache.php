<?php

namespace PortedCheese\Catalog\Listeners;

use App\Category;
use PortedCheese\Catalog\Events\ProductListChange;

class ProductFilterClearCache
{
    /**
     * Категория.
     *
     * @var null|Category
     */
    protected $category;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->category = null;
    }

    /**
     * Handle the event.
     *
     * @param ProductListChange $event
     * @return void
     */
    public function handle(ProductListChange $event)
    {
        $product = $event->product;
        $this->category = $product->category;
        $this->clearCategoryCache();
        $this->getOriginalCategory($event);
    }

    /**
     * Очистить кэши у категории.
     */
    private function clearCategoryCache()
    {
        if (empty($this->category)) {
            return;
        }
        $this->category->forgetFilterPIdsCache();
        $this->category->forgetProductValuesCache();
        $this->category->forgetFilterVariationsCache();
    }

    /**
     * Найти старую категорию.
     *
     * @param ProductListChange $event
     */
    private function getOriginalCategory(ProductListChange $event)
    {
        $categoryId = $event->categoryId;
        if (empty($categoryId)) {
            return;
        }
        try {
            $this->category = Category::findOrFail($categoryId);
        }
        catch (\Exception $e) {
            $this->category = null;
        }
        $this->clearCategoryCache();
    }
}
