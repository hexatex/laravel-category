<?php

namespace Hexatex\LaravelCategory\Factories;

use Hexatex\LaravelCategory\CategoryType\CategoryType;
use Hexatex\LaravelCategory\Category\Category;
use Hexatex\LaravelImage\Image\Contracts\Image;
use Hexatex\LaravelMetadata\Metadata\Contracts\Metadata;
use Hexatex\LaravelSlug\Slug\Contracts\Slug;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'page_title' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'sort_by' => $this->faker->sentence,
            'sort_desc' => $this->faker->boolean,
            'is_hidden' => false,
        ];
    }

    /*
     * States
     */
    public function hidden()
    {
        return $this->state(function (array $attributes) {
            return [
                'hidden' => true,
            ];
        });
    }

    public function type(?CategoryType $type)
    {
        return $this->afterMaking(function (Category $category) use ($type) {
            $category->type()->associate($type);
        });
    }

    public function mainImage(?Image $mainImage)
    {
        return $this->afterMaking(function (Category $category) use ($mainImage) {
            $category->mainImage()->associate($mainImage);
        });
    }

    public function parent(?Category $parent)
    {
        return $this->afterMaking(function (Category $category) use ($parent) {
            $category->parent()->associate($parent);
        });
    }

    public function metadata(Metadata $metadata)
    {
        return $this->afterMaking(function (Category $category) use ($metadata) {
            $category->metadata()->associate($metadata);
        });
    }

    public function slug(Slug $slug)
    {
        return $this->afterMaking(function (Category $category) use ($slug) {
            $category->slug()->associate($slug);
        });
    }
}
