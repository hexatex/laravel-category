<?php

namespace Hexatex\LaravelCategory\CategoryItem\Resources\Admin;

use Hexatex\LaravelCategory\AdminCategorizableResources;
use Hexatex\LaravelCategory\Category\Resources\Admin\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            /* Relationships */
            'category' => new CategoryResource($this->whenLoaded('category')),
            'categorizable' => AdminCategorizableResources::single($this->whenLoaded('categorizable')),
        ];
    }
}
