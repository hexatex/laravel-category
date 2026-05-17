<?php

namespace Hexatex\LaravelCategory\Category\Contracts;

use Hexatex\LaravelCategory\CategoryItem\Contracts\CategoryItem;
use Hexatex\LaravelCategory\CategoryType\CategoryType;
use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelImage\Image\Contracts\Imageable;
use Hexatex\LaravelMetadata\Metadata\Contracts\Metadatable;
use Hexatex\LaravelMisc\Contracts\Model;
use Hexatex\LaravelSlug\Slug\Contracts\Sluggable;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $title
 * @property string $page_title
 * @property string $description
 * @property string $sort_by
 * @property bool $sort_desc
 * @property bool $is_hidden
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * Relationships
 * @property-read CategoryType|null $type
 * @property-read Image|null $main_image
 * @property-read Category|null $parent
 * @property-read Category[]|Collection $children
 * @property-read CategoryItem[]|Collection $items
 */
interface Category extends Model, Imageable, Metadatable, Sluggable
{
    public function type(): Builder;
    public function mainImage(): Builder;
    public function parent(): Builder;
    public function children(): Builder;
    public function items(): Builder;
}
