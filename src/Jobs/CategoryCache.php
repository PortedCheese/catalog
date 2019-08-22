<?php

namespace PortedCheese\Catalog\Jobs;

use App\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CategoryCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const METHODS = [
        'getFieldsInfo',
        'getChildrenFieldsFilterInfo',
        'getTeaser',
        'getSiteBreadcrumb',
        'getChildren',
        'getProductValues',
        'getPIds',
        'addPriceFilter',
    ];

    protected $category;
    protected $method;

    /**
     * CategoryCache constructor.
     * @param Category $category
     * @param $method
     */
    public function __construct(Category $category, $method)
    {
        $this->category = $category;
        $this->method = $method;
    }

    /**
     * Execute the job.
     *
     * @throws \Throwable
     */
    public function handle()
    {
        if (! empty(in_array($this->method, self::METHODS))) {
            $this->runMethod();
        }
    }

    /**
     * Запускаем метод у категории что бы закешировать.
     *
     * @throws \Throwable
     */
    private function runMethod()
    {
        switch ($this->method) {
            case "getFieldsInfo":
                $this->category->getFieldsInfo();
                break;

            case "getChildrenFieldsFilterInfo":
                $this->category->getChildrenFieldsFilterInfo();
                break;

            case "getTeaser":
                $this->category->getTeaser();
                break;

            case "getSiteBreadcrumb":
                $this->category->getSiteBreadcrumb();
                break;

            case "getChildren":
                $this->category->getChildren();
                break;

            case "getProductValues":
            case "getPIds":
            case "addPriceFilter":
                $this->category->getFilters();
                $this->category->getFilters(true);
                break;
        }
    }
}
