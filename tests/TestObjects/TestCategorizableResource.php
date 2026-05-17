<?php

namespace Hexatex\LaravelCategory\Tests\TestObjects;

use Illuminate\Http\Resources\Json\JsonResource;

class TestCategorizableResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'morph_class' => $this->getMorphClass(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            /* Relationships */
            // 'categoryItems' => CategoryItemResource::collection($this->whenLoaded('categoryItems')), // todo Create the resource for Cat Items and use it here. This relationship is not needed in the resource for testing, but maybe a good idea to keep it in the resource to be less confusing to others.
        ];
    }
}
