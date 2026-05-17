<?php

namespace Hexatex\LaravelCategory\Category;

use Hexatex\LaravelCategory\CategoryItem\CategoryItem;
use Hexatex\LaravelCategory\Category\Contracts\Category as CategoryContract;
use Hexatex\LaravelImage\Image\Contracts\Imageable;
use Hexatex\LaravelImage\Image\Image;
use Hexatex\LaravelMetadata\Metadata\Metadata;
use Hexatex\LaravelMisc\Model;
use Hexatex\LaravelSlug\Slug\Slug;
use Illuminate\Contracts\Database\Query\Builder;

class Category extends Model implements CategoryContract
{
    protected $fillable = [
        'title',
        'page_title',
        'description',
        'sort_by',
        'sort_desc',
        'is_hidden',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
        'sort_desc' => 'boolean',
    ];

    /*
     * Imageable
     */
    public function images(): Builder
    {
        return $this->morphToMany(Image::class, 'imageable');
    }

    /*
     * Metadatable
     */
    public function metadata(): Builder
    {
        return $this->belongsTo(Metadata::class);
    }

    /*
     * Sluggable
     */
    /**
     * Get the default slug when none is provided during creation.
     *
     * @return string The default slug value.
     */
    public function getDefaultSlug(): string
    {
        return $this->title;
    }

    public function slug(): Builder
    {
        return $this->morphOne(Slug::class, 'sluggable');
    }

    /*
     * Relationships
     */
    public function type(): Builder
    {
        return $this->morphTo();
    }

    public function mainImage(): Builder
    {
        return $this->belongsTo(Image::class);
    }

    public function parent(): Builder
    {
        return $this->belongsTo(Category::class);
    }

    public function children(): Builder
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function items(): Builder
    {
        return $this->hasMany(CategoryItem::class);
    }
}
