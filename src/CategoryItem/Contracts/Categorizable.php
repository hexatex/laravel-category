<?php

namespace Hexatex\LaravelCategory\CategoryItem\Contracts;

use Hexatex\LaravelMisc\Contracts\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * Categorizable is meant to be implemented by any model you wish to categorize
 *
 * @property int $id
 * 
 * Relationships
 * @property-read CategoryItem[]|Collection $categoryItems
 */
interface Categorizable extends Model
{
    public function categoryItems(): Builder;
}
