<?php

namespace Hexatex\LaravelCategory\Tests\Feature\Category;

use Hexatex\LaravelCategory\Category\Category;
use Hexatex\LaravelCategory\Category\Resources\Web\CategoryResource;
use Hexatex\LaravelCategory\Factories\CategoryFactory;
use Hexatex\LaravelCategory\Factories\TestCategoryTypeFactory;
use Hexatex\LaravelCategory\Tests\FeatureTestCase;
use Hexatex\LaravelImage\Factories\ImageFactory;
use Hexatex\LaravelMetadata\Factories\MetadataFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryWebApiTest extends FeatureTestCase
{
    use RefreshDatabase;

    public function testGetCategory()
    {
        $categoryType = TestCategoryTypeFactory::new()->create();
        $mainImage = ImageFactory::new()->author($this->user)->create();
        $parentMetadata = MetadataFactory::new()->create();
        $parent = CategoryFactory::new()->metadata($parentMetadata)->create();
        $metadata = MetadataFactory::new()->create();
        $category = CategoryFactory::new()
            ->metadata($metadata)
            ->type($categoryType)
            ->mainImage($mainImage)
            ->parent($parent)
            ->create();

        $childMetadata = MetadataFactory::new()->create();
        $child = CategoryFactory::new()->metadata($childMetadata)->parent($category)->create();

        $response = $this->getJson("api/category/{$category->id}");

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'page_title',
                    'description',
                    'sort_by',
                    'sort_desc',

                    /* Relationships */
                    'type' => [
                        'id',
                        'morph_class',
                        'created_at',
                        'updated_at',
                    ],
                    'mainImage' => [
                        'id',
                        'alt',
                        'original_filename',
                        'url',
                        'thumbnail_url',
                    ],
                    'parent' => [
                        'id',
                        'title',
                        'page_title',
                        'description',
                        'sort_by',
                        'sort_desc',
                    ],
                    'children' => [
                        [
                            'id',
                            'title',
                            'page_title',
                            'description',
                            'sort_by',
                            'sort_desc',
                        ],
                    ],
                ],
            ])->assertJsonFragment([
                'id' => $category->id,
                'title' => $category->title,
                'page_title' => $category->page_title,
                'description' => $category->description,
                'sort_by' => $category->sort_by,
                'sort_desc' => $category->sort_desc,
            ])->assertJsonFragment([
                'id' => $categoryType->id,
                'morph_class' => $categoryType->getMorphClass(),
                'test' => $categoryType->test,
                'created_at' => $categoryType->created_at,
                'updated_at' => $categoryType->updated_at,
            ])->assertJsonFragment([
                'id' => $mainImage->id,
                'alt' => $mainImage->alt,
            ])->assertJsonFragment([
                'id' => $parent->id,
                'created_at' => $parent->created_at,
                'updated_at' => $parent->updated_at,
            ])->assertJsonFragment([
                'id' => $child->id,
                'created_at' => $child->created_at,
                'updated_at' => $child->updated_at,
            ])
            ->assertJsonMissingPath('data.is_hidden')
            ->assertJsonMissingPath('data.created_at')
            ->assertJsonMissingPath('data.updated_at')

            // parent
            ->assertJsonMissingPath('data.parent.is_hidden')
            ->assertJsonMissingPath('data.parent.created_at')
            ->assertJsonMissingPath('data.parent.updated_at')

            // children.0
            ->assertJsonMissingPath('data.children.0.is_hidden')
            ->assertJsonMissingPath('data.children.0.created_at')
            ->assertJsonMissingPath('data.children.0.updated_at')

            // mainImage
            ->assertJsonMissingPath('data.mainImage.disk')
            ->assertJsonMissingPath('data.mainImage.created_at')
            ->assertJsonMissingPath('data.mainImage.updated_at');
    }
}
