<?php

namespace Hexatex\LaravelCategory\Tests\TestObjects;

use Hexatex\LaravelCategory\Category\Resources\Admin\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TestCategoryTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'morph_class' => $this->getMorphClass(),
            'test' => $this->test,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            /* Relationships */
            'category' => CategoryResource::collection($this->whenLoaded('categoryItems')), // todo Create the resource for Cat Items and use it here. This relationship is not needed in the resource for testing, but maybe a good idea to keep it in the resource to be less confusing to others.
        ];
    }
}
