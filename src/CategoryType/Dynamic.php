<?php

namespace Hexatex\LaravelCategory\CategoryType;

use Hexatex\LaravelCategory\Category;
use Hexatex\LaravelCriteria\Criteriable;
use Hexatex\LaravelCriteria\Criteria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Model;

/**
 * Hexatex\LaravelCategory\CategoryType\Dynamic
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Hexatex\LaravelCategory\Category $category
 * @property-read \Hexatex\LaravelCriteria\Criteria $criteria
 * @property-read string $display_name
 * @property-read string $url
 * @method static \Illuminate\Database\Eloquent\Builder|\Hexatex\LaravelCategory\CategoryType\Dynamic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Hexatex\LaravelCategory\CategoryType\Dynamic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Hexatex\LaravelCategory\CategoryType\Dynamic query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Hexatex\LaravelCategory\CategoryType\Dynamic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Hexatex\LaravelCategory\CategoryType\Dynamic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Hexatex\LaravelCategory\CategoryType\Dynamic whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Dynamic extends Model implements CategoryType, Criteriable, CategoryType
{
    /** @var string */
    protected $table = 'category_dynamics';

    /*
     * CategoryType
     */
    /**
     * Category
     * @return MorphOne
     */
    public function category()
    {
        return $this->morphOne(Category::class, 'category_type');
    }

    /*
     * Criteriable
     */
    /**
     * Criteria
     * @return MorphOne
     */
    public function criteria()
    {
        return $this->morphOne(Criteria::class, 'criteriable');
    }
}
