<?php

namespace PortedCheese\Catalog\Http\Services;

use App\Category;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductFilterService
{
    protected $request;
    protected $category;
    protected $categoryIds;
    protected $having;
    protected $setHaving;
    protected $products;
    protected $machineId;
    protected $ranges;

    public function __construct(Request $request, Category $category, $setHaving = false)
    {
        $this->request = $request;
        $this->category = $category;
        $this->categoryIds = $this->category->getChildren(true);
        $this->having = [];
        $this->setHaving = $setHaving;
        $this->machineId = [];
        $this->ranges = [];
        // Инициализация запроса.
        // Только опубликованные товары и относящиеся к текущей категории и ее подкатегориям.
        $this->products = Product::query()
            ->where('products.published', 1)
            ->whereIn('products.category_id', $this->categoryIds);
        // Получить информацию о полях категории.
        $fieldsInfo = $this->category->getChildrenFieldsFilterInfo();
        foreach ($fieldsInfo as $item) {
            $this->machineId[$item->machine] = [
                'id' => $item->id,
                'type' => $item->type,
            ];
        }
    }

    /**
     * Запуск фильтра.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function makeFilter()
    {
        $query = $this->request->query;
        // Фильтрация.
        foreach ($query->all() as $key => $value) {
            if (empty($value)) {
                continue;
            }
            if ($this->makeSelect($key, $value)) {
                continue;
            }
            
            if ($this->makeCheckbox($key, $value)) {
                continue;
            }
            
            if ($this->makeRange($key, $value)) {
                continue;
            }
        }
        $this->filterRanges();
        $this->products->groupBy('products.id');
        // Ограничиваем значения для чекбокса.
        if (!empty($this->having)) {
            $this->products->havingRaw(implode(" and ", $this->having));
        }

        Product::addSortFromFilter($this->request, $this->products);
        $perPage = siteconf()->get("catalog", "productsSitePager");

        return $this->products->paginate($perPage)->appends($this->request->input());
    }

    /**
     * Фильтрация диапазона.
     */
    protected function filterRanges()
    {
        foreach ($this->ranges as $machine => $range) {
            if ($machine == 'product_price') {
                $ranges = Product::queryRangeVariations($range['from'], $range['to']);
            }
            else {
                $fieldId = $this->machineId[$machine]['id'];
                $ranges = Product::queryRangeFields($range['from'], $range['to'], $fieldId);
            }
            $this->products->joinSub($ranges,  $machine, function ($join) use ($machine) {
                $join->on("products.id", '=', "{$machine}.product_id");
            });
        }
        if (! base_config()->get("catalog", "disablePriceSort")) {
            // Если нет фильтра по цене, нужно добавить цены, что бы работала сортировка.
            if (empty($this->ranges['product_price'])) {
                $prices = Product::queryRangeVariations(0,0, false);
                $this->products->joinSub($prices, "product_price", function ($join) {
                    $join->on("products.id", '=', "product_price.product_id");
                });
            }
        }
    }

    /**
     * Обработка диапазона.
     * 
     * @param $key
     * @param $value
     * @return bool
     */
    protected function makeRange($key, $value)
    {
        if (strstr($key, 'range-') !== FALSE) {
            $sub = str_replace("range-", '', $key);
            if (strstr($sub, "from-") !== FALSE) {
                $operator = "from";
                $machine = str_replace("from-", '', $sub);
            }
            elseif (strstr($sub, "to-") !== FALSE) {
                $operator = "to";
                $machine = str_replace("to-", '', $sub);
            }
            if (empty($this->machineId[$machine]) && $machine != 'product_price') {
                return true;
            }
            if (empty($this->ranges[$machine])) {
                $this->ranges[$machine] = [
                    'from' => false,
                    'to' => false,
                ];
            }
            $this->ranges[$machine][$operator] = (int) $value;
        }
        else {
            return false;
        }
        return true;
    }

    /**
     * Обработка чекбоксов.
     * 
     * @param $key
     * @param $value
     * @return bool
     */
    protected function makeCheckbox($key, $value)
    {
        if (strstr($key, 'check-') !== FALSE) {
            $machine = str_replace('check-', '', $key);
            if (empty($this->machineId[$machine])) {
                return true;
            }
            $fieldId = $this->machineId[$machine]['id'];
            $checkboxes = Product::queryCheckFields($value, $fieldId);

            $this->products->joinSub($checkboxes, $machine, function ($join) use ($machine) {
                $join->on("products.id", '=', "{$machine}.product_id");
            });
            if ($this->setHaving) {
                // Это для того что бы проверял все значения а не по одному.
                $this->having[] = "max({$machine}.count) = " . count($value);
            }
        }
        else {
            return false;
        }
        return true;
    }

    /**
     * Обработка селекта.
     * 
     * @param $key
     * @param $value
     * @return bool
     */
    protected function makeSelect($key, $value)
    {
        if (strstr($key, 'select-') !== FALSE) {
            $machine = str_replace('select-', '', $key);
            if (empty($this->machineId[$machine])) {
                return true;
            }
            $fieldId = $this->machineId[$machine]['id'];
            $selects = Product::querySelectFields($value, $fieldId);

            $this->products->joinSub($selects,  $machine, function ($join) use ($machine) {
                $join->on("products.id", '=', "{$machine}.product_id");
            });
        }
        else {
            return false;
        }
        return true;
    }
}