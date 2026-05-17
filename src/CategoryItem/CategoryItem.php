<?php

namespace Hexatex\LaravelCategory\CategoryItem;

use Hexatex\LaravelCategory\Category\Category;
use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItem as CategoryItemContract;
use Hexatex\LaravelFiltered\Filtered;
use Hexatex\LaravelMisc\Model;
use Illuminate\Contracts\Database\Query\Builder;

class CategoryItem extends Model implements CategoryItemContract
{
    /*
     * Relationships
     */
    public function category(): Builder
    {
        return $this->belongsTo(Category::class);
    }

    public function categorizable(): Builder
    {
        return $this->morphTo();
    }
}
