<?php

namespace Hexatex\LaravelCategory\CategoryType;

use Hexatex\LaravelCategory\Category;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Hexatex\LaravelCategory\CategoryType\Regular
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Hexatex\LaravelCategory\Category $category
 * @property-read string $display_name
 * @property-read string $url
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Hexatex\LaravelCategory\CategoryType\Regular newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Hexatex\LaravelCategory\CategoryType\Regular newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Hexatex\LaravelCategory\CategoryType\Regular query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Hexatex\LaravelCategory\CategoryType\Regular whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Hexatex\LaravelCategory\CategoryType\Regular whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Hexatex\LaravelCategory\CategoryType\Regular whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Regular extends CategoryType implements CategoryType
{
    /**
     * @var string
     */
    protected $table = 'category_regulars';

    /*
     * CategoryType
     */
    /**
     * Category
     *
     * @return MorphOne
     */
    public function category()
    {
        return $this->morphOne(Category::class, 'category_type');
    }
}
