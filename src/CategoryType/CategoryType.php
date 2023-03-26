<?php

namespace Hexatex\LaravelCategory\CategoryType;

use Hexatex\LaravelCategory\Category;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Interface CategoryType
 *
 * @property Category $category
 */
interface CategoryType
{
    /**
     * Category
     *
     * @return MorphOne
     */
    public function category();
}
