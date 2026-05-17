<?php

namespace Hexatex\LaravelCategory\Tests\TestObjects;

use Hexatex\LaravelCategory\CategoryType\CategoryType;
use Hexatex\LaravelCategory\Category\Category;
use Hexatex\LaravelMisc\Model;
use Illuminate\Contracts\Database\Query\Builder;

class TestCategoryType extends Model implements CategoryType
{
    protected $fillable = ['test'];

    /*
     * CategoryType
     */
    public function category(): Builder
    {
        return $this->morphOne(Category::class);
    }
}
