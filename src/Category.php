<?php

namespace Hexatex\LaravelCategory;

use Hexatex\LaravelBreadcrumbs\Breadcrumbable;
use Hexatex\LaravelCategory\CategoryType\CategoryType;
use Hexatex\LaravelFacet\Facet;
use Hexatex\LaravelFacet\Facetable;
use Hexatex\LaravelFiltered\Traits\Filtered;
use Hexatex\LaravelImage\Image;
use Hexatex\LaravelMenu\Menu;
use Hexatex\LaravelSlug\HasSlug;
use Hexatex\LaravelSlug\Slugable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Category extends Model implements Breadcrumbable, Slugable, Facetable
{
    use Filtered, HasSlug;

    protected $fillable = [
        'name',
        'description',
        'short_description',
        'menu_name',
        'page_title',
        'meta_description',
        'meta_keywords',
        'keywords',
        'sort_by',
        'sort_desc',
        'allow_resorting',
        'is_hidden',
        'in_top_menu',
        'display_order',

        /* Slugable */
        'slug',
    ];

    /*
     * Breadcrumbable
     */
    /**
     * Display name
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->menu_name ?: $this->name;
    }

    /**
     * Url
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return '/'.$this->getSlug();
    }

    /**
     * Get Breadcrumbable breadcrumb
     *
     * @return Breadcrumbable
     */
    public function getBreadcrumb()
    {
        return $this->parent;
    }

    /*
     * Slugable
     * (Shares URL accessor with Breadcrumbable)
     */
    /**
     * Return the models Url slug (auto or from slug column)
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug ?: "{$this->menu_name}__{$this->id}";
    }

    /*
     * Facetable
     */
    /**
     * Facets for Facet search
     *
     * @return HasMany
     */
    public function facets()
    {
        // Todo: this needs to be polymorphic
        return $this->hasMany(Facet::class);
    }

    /*
     * Scopes
     */
    /**
     * Scope by parent Category
     */
    public function scopeByParent(Builder $query, ?Category $parent): void
    {
        if ($parent) {
            $query->where('parent_id', $parent->id);
        } else {
            $query->whereNull('parent_id');
        }
    }

    /*
     * Relationships
     */
    /**
     * Implements CategoryType
     *
     * @return MorphTo
     */
    public function categoryType()
    {
        return $this->morphTo();
    }

    /**
     * Parent Category
     *
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Child Categories
     *
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * CategoryItems
     *
     * @return BelongsToMany
     */
    public function categoryItems()
    {
        $sortBy = $this->sort_by ?? 'category_category_item.display_order';
        $sortDirection = $this->sort_desc === true ? 'desc' : 'asc';

        return $this->belongsToMany(CategoryItem::class)
            ->select('category_items.*')
            ->orderBy($sortBy, $sortDirection)
            ->withPivot('display_order');
    }

    /**
     * Menu Image
     *
     * @return BelongsTo
     */
    public function menuImage()
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * MenusItems assigning the Category to Menus
     *
     * @return HasMany
     */
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
