<?php

namespace Hexatex\LaravelCategory\Tests\Feature\Category;

use Hexatex\LaravelCategory\Category\Category;
use Hexatex\LaravelCategory\Category\Resources\Admin\CategoryResource;
use Hexatex\LaravelCategory\Factories\CategoryFactory;
use Hexatex\LaravelCategory\Factories\TestCategoryTypeFactory;
use Hexatex\LaravelCategory\Tests\FeatureTestCase;
use Hexatex\LaravelImage\Factories\ImageFactory;
use Hexatex\LaravelMetadata\Factories\MetadataFactory;
use Hexatex\LaravelSlug\Factories\SlugFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryAdminApiTest extends FeatureTestCase
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
            ->type($categoryType)
            ->metadata($metadata)
            ->mainImage($mainImage)
            ->parent($parent)
            ->create();

        $slug = SlugFactory::new()->sluggable($category)->create();

        $childMetaData = MetadataFactory::new()->create();
        $child = CategoryFactory::new()->parent($category)->metadata($metadata)->create();

        $response = $this->getJson("admin/api/category/{$category->id}");

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'page_title',
                    'description',
                    'sort_by',
                    'sort_desc',
                    'is_hidden',
                    'created_at',
                    'updated_at',

                    /* Relationships */
                    'type' => [
                        'id',
                        'morph_class',
                        'created_at',
                        'updated_at',
                    ],
                    'mainImage' => [
                        'id',
                        'created_at',
                        'updated_at',
                    ],
                    'metadata' => ['id'],
                    'slug' => ['id'],
                    'parent' => [
                        'id',
                        'created_at',
                        'updated_at',
                    ],
                    'children' => [
                        [
                            'id',
                            'created_at',
                            'updated_at',
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
                'is_hidden' => $category->is_hidden,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
            ])->assertJsonFragment([
                'id' => $categoryType->id,
                'morph_class' => $categoryType->getMorphClass(),
                'test' => $categoryType->test,
                'created_at' => $categoryType->created_at,
                'updated_at' => $categoryType->updated_at,
            ])->assertJsonFragment([
                'id' => $mainImage->id,
                'created_at' => $mainImage->created_at,
                'updated_at' => $mainImage->updated_at,
            ])->assertJsonFragment([
                'id' => $parent->id,
                'created_at' => $parent->created_at,
                'updated_at' => $parent->updated_at,
            ])->assertJsonFragment([
                'id' => $child->id,
                'created_at' => $child->created_at,
                'updated_at' => $child->updated_at,
            ])->assertJsonFragment([
                'id' => $slug->id,
                'slug' => $slug->slug,
                'created_at' => $slug->created_at,
                'updated_at' => $slug->updated_at,
            ])->assertJsonFragment([
                'id' => $metadata->id,
                'page_title' => $metadata->page_title,
                'description' => $metadata->description,
                'keywords' => $metadata->keywords,
                'author' => $metadata->author,
                'copyright' => $metadata->copyright,
                'created_at' => $metadata->created_at,
                'updated_at' => $metadata->updated_at,
            ]);
    }

    public function testStoreCategory(bool $hasImage = true, bool $hasParent = true, bool $hasCategoryType = true)
    {
        $mainImage = $hasImage
            ? ImageFactory::new()->author($this->user)->create()
            : null;

        $parentMetadata = MetadataFactory::new()->create();

        $parent = $hasImage
            ? CategoryFactory::new()->metadata($parentMetadata)->create()
            : null;

        $categoryType = null;

        if ($hasCategoryType) {
            $categoryType = TestCategoryTypeFactory::new()->make();
            $categoryType->id = 12345;
        }

        $fillMetadata = MetadataFactory::new()->make();
        $fillMetadata->id = 1234;

        $fillCategory = CategoryFactory::new()
            ->metadata($fillMetadata)
            ->mainImage($mainImage)
            ->parent($parent)
            ->type($categoryType)
            ->make();

        $fillSlug = SlugFactory::new()->make();
        $fillCategory->setRelation('slug', $fillSlug);

        $fill = $this->resourceToArray(new CategoryResource($fillCategory));

        $response = $this->postJson("admin/api/category", $fill);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'page_title',
                    'description',
                    'sort_by',
                    'sort_desc',
                    'is_hidden',
                    'created_at',
                    'updated_at',
                    'metadata' => ['id'],
                    'slug' => ['id'],
                ],
            ])->assertJsonFragment([
                'title' => $fill['title'],
                'page_title' => $fill['page_title'],
                'description' => $fill['description'],
                'sort_by' => $fill['sort_by'],
                'sort_desc' => $fill['sort_desc'],
                'is_hidden' => $fill['is_hidden'],
            ])->assertJsonFragment([
                'slug' => $fillSlug->slug,
            ])->assertJsonFragment([
                'page_title' => $fillMetadata->page_title,
                'description' => $fillMetadata->description,
                'keywords' => $fillMetadata->keywords,
                'author' => $fillMetadata->author,
                'copyright' => $fillMetadata->copyright,
            ]);

        $data = $response->getData()->data;

        if ($hasImage) {
            $response->assertSuccessful()
                ->assertJsonStructure([
                    'data' => [
                        'mainImage' => [
                            'id',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ])->assertJsonFragment([
                    'id' => $mainImage->id,
                    'created_at' => $mainImage->created_at,
                    'updated_at' => $mainImage->updated_at,
                ]);
        } else {
            $response->assertJsonFragment(['id' => $data->id, 'mainImage' => null]);
        }

        if ($hasParent) {
            $response->assertSuccessful()
                ->assertJsonStructure([
                    'data' => [
                        'parent' => [
                            'id',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ])->assertJsonFragment([
                    'id' => $parent->id,
                    'created_at' => $parent->created_at,
                    'updated_at' => $parent->updated_at,
                ]);
        } else {
            $response->assertJsonFragment(['id' => $data->id, 'parent' => null]);
        }

        if ($hasCategoryType) {
            $response->assertSuccessful()
                ->assertJsonStructure([
                    'data' => [
                        'type' => [
                            'id',
                            'morph_class',
                            'test',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ])->assertJsonFragment([
                    'morph_class' => $categoryType->getMorphClass(),
                    'test' => $categoryType->test,
                ]);
        } else {
            $response->assertJsonFragment(['id' => $data->id, 'type' => null]);
        }
    }

    public function testStoreCategory_noImage_noParent_noCategoryType()
    {
        $this->testStoreCategory(hasImage: false, hasParent: false, hasCategoryType: false);
    }


    public function testUpdateCategory(bool $hasImage = true, bool $hasParent = true, bool $hasCategoryType = true)
    {
        $mainImage = $hasImage
            ? ImageFactory::new()->author($this->user)->create()
            : null;

        $parentMetadata = MetadataFactory::new()->create();

        $parent = $hasImage
            ? CategoryFactory::new()->metadata($parentMetadata)->create()
            : null;

        $fillCategoryType = null;

        if ($hasCategoryType) {
            $fillCategoryType = TestCategoryTypeFactory::new()->make();
            $fillCategoryType->id = 12345;
        }

        $metadata = MetadataFactory::new()->create();
        $categoryType = $hasCategoryType ? TestCategoryTypeFactory::new()->create() : null;
        $category = CategoryFactory::new()->type($categoryType)->metadata($metadata)->create();

        $slug = SlugFactory::new()->sluggable($category)->create();

        $fillMetadata = MetadataFactory::new()->make();
        $fillMetadata->id = 1234;

        $fillCategory = CategoryFactory::new()
            ->metadata($fillMetadata)
            ->mainImage($mainImage)
            ->parent($parent)
            ->type($fillCategoryType)
            ->make();

        $fillSlug = SlugFactory::new()->make();
        $fillCategory->setRelation('slug', $fillSlug);

        $fill = $this->resourceToArray(new CategoryResource($fillCategory));

        $response = $this->putJson("admin/api/category/{$category->id}", $fill);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'page_title',
                    'description',
                    'sort_by',
                    'sort_desc',
                    'is_hidden',
                    'created_at',
                    'updated_at',
                    'metadata' => ['id'],
                    'slug' => ['id'],
                ],
            ])->assertJsonFragment([
                'title' => $fill['title'],
                'page_title' => $fill['page_title'],
                'description' => $fill['description'],
                'sort_by' => $fill['sort_by'],
                'sort_desc' => $fill['sort_desc'],
                'is_hidden' => $fill['is_hidden'],
            ])->assertJsonFragment([
                'slug' => $fillSlug->slug,
            ])->assertJsonFragment([
                'page_title' => $fillMetadata->page_title,
                'description' => $fillMetadata->description,
                'keywords' => $fillMetadata->keywords,
                'author' => $fillMetadata->author,
                'copyright' => $fillMetadata->copyright,
            ]);

        $data = $response->getData()->data;

        if ($hasImage) {
            $response->assertSuccessful()
                ->assertJsonStructure([
                    'data' => [
                        'mainImage' => [
                            'id',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ])->assertJsonFragment([
                    'id' => $mainImage->id,
                    'created_at' => $mainImage->created_at,
                    'updated_at' => $mainImage->updated_at,
                ]);
        } else {
            $response->assertJsonFragment(['id' => $data->id, 'mainImage' => null]);
        }

        if ($hasParent) {
            $response->assertSuccessful()
                ->assertJsonStructure([
                    'data' => [
                        'parent' => [
                            'id',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ])->assertJsonFragment([
                    'id' => $parent->id,
                    'created_at' => $parent->created_at,
                    'updated_at' => $parent->updated_at,
                ]);
        } else {
            $response->assertJsonFragment(['id' => $data->id, 'parent' => null]);
        }

        if ($hasCategoryType) {
            $response->assertSuccessful()
                ->assertJsonStructure([
                    'data' => [
                        'type' => [
                            'id',
                            'morph_class',
                            'test',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ])->assertJsonFragment([
                    'id' => $categoryType->id,
                    'morph_class' => $categoryType->getMorphClass(),
                    'test' => $fillCategoryType->test,
                ]);
        } else {
            $response->assertJsonFragment(['id' => $data->id, 'type' => null]);
        }
    }

    public function testUpdateCategory_noImage_noParent_noCategoryType()
    {
        $this->testUpdateCategory(hasImage: false, hasParent: false, hasCategoryType: false);
    }

    public function testDestroyCategory()
    {
        $metadata = MetadataFactory::new()->create();
        $image = ImageFactory::new()->author($this->user)->create();
        $category = CategoryFactory::new()->metadata($metadata)->mainImage($image)->create();
        $category->images()->attach($image);

        $slug = SlugFactory::new()->sluggable($category)->create();

        $response = $this->deleteJson("admin/api/category/{$category->id}");

        $response->assertSuccessful()->assertJsonStructure([]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('metadata', ['id' => $metadata->id]);
        $this->assertDatabaseMissing('slugs', ['id' => $slug->id]);
        $this->assertDatabaseMissing('imageables', [
            'imageable_id' => $category->id,
            'imageable_type' => $category->getMorphClass(),
            'image_id' => $image->id,
        ]);
    }
}
