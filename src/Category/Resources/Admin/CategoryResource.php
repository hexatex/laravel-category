<?php

namespace Hexatex\LaravelCategory\Category\Resources\Admin;

use Hexatex\LaravelCategory\AdminCategoryTypeResources;
use Hexatex\LaravelCategory\AdminCategoryableResources;
use Hexatex\LaravelCategory\CategoryItem\Resources\Admin\CategoryItemResource;
use Hexatex\LaravelImage\Image\Resources\Admin\ImageResource;
use Hexatex\LaravelMetadata\Metadata\Resources\Admin\MetadataResource;
use Hexatex\LaravelSlug\Slug\Resources\Admin\SlugResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'page_title' => $this->page_title,
            'description' => $this->description,
            'sort_by' => $this->sort_by,
            'sort_desc' => $this->sort_desc,
            'is_hidden' => $this->is_hidden,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            /* Imageable */
            'images' => ImageResource::collection($this->whenLoaded('images')), // todo make sure api tests test this, it isn't now

            /* Metadatable */
            'metadata' => new MetadataResource($this->whenLoaded('metadata')),

            /* Sluggable */
            'slug' => new SlugResource($this->whenLoaded('slug')),

            /* Relationships */
            'type' => AdminCategoryTypeResources::single($this->whenLoaded('type')),
            'mainImage' => new ImageResource($this->whenLoaded('mainImage')),
            'parent' => new CategoryResource($this->whenLoaded('parent')),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'items' => CategoryItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
