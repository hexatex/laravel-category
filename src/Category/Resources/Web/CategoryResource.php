<?php

namespace Hexatex\LaravelCategory\Category\Resources\Web;

use Hexatex\LaravelCategory\CategoryItem\Resources\Web\CategoryItemResource;
use Hexatex\LaravelCategory\WebCategoryTypeResources;
use Hexatex\LaravelCategory\WebCategoryableResources;
use Hexatex\LaravelImage\Image\Resources\Web\ImageResource;
use Hexatex\LaravelSlug\Slug\Resources\Web\SlugResource;
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

            /* Sluggable */
            'slug' => new SlugResource($this->whenLoaded('slug')),

            /* Relationships */
            'type' => WebCategoryTypeResources::single($this->whenLoaded('type')),
            'mainImage' => new ImageResource($this->whenLoaded('mainImage')),
            'parent' => new CategoryResource($this->whenLoaded('parent')),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'items' => CategoryItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
