<?php

namespace Hexatex\LaravelCategory\CategoryItem\Resources\Web;

use Hexatex\LaravelCategory\WebCategorizableResources;
use Hexatex\LaravelCategory\Category\Resources\Web\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            /* Relationships */
            'category' => new CategoryResource($this->whenLoaded('category')),
            'categorizable' => WebCategorizableResources::single($this->whenLoaded('categorizable')),
        ];
    }
}
