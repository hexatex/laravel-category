<?php

namespace Hexatex\LaravelCategory\Tests\TestObjects;

use Hexatex\LaravelCategory\CategoryItem\Contracts\Categorizable;
use Hexatex\LaravelCategory\Category\Category;
use Hexatex\LaravelMisc\Model;
use Illuminate\Contracts\Database\Query\Builder;

class TestCategorizable extends Model implements Categorizable
{
    protected $fillable = [];

    /*
     * Categorizable
     */
    public function categoryItems(): Builder
    {
        return $this->morphMany(CategoryItem::class);
    }
}
