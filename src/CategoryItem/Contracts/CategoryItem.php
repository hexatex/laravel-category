<?php

namespace Hexatex\LaravelCategory\CategoryItem\Contracts;

use Hexatex\LaravelCategory\CategoryItem\Contracts\Categorizable;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelMisc\Contracts\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * Relationships
 * @property-read Category $category
 * @property-read Categorizable $categorizable
 */
interface CategoryItem extends Model
{
    public function category(): Builder;
    public function categorizable(): Builder;
}
