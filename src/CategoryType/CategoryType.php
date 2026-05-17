<?php

namespace Hexatex\LaravelCategory\CategoryType;

use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelMisc\Contracts\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * The CategoryType is an optional relationship for Categories and allows
 * for multiple types of Categories with special attributes.
 * 
 * @property int $id
 * 
 * Relationships
 * @property-read Category $category
 */
interface CategoryType extends Model
{
    public function category(): Builder;
}
