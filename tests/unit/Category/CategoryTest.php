<?php

namespace Hexatex\LaravelCategory\Tests\Unit\Category;

use Hexatex\LaravelCategory\CategoryItem\CategoryItem;
use Hexatex\LaravelCategory\Category\Category;
use Hexatex\LaravelCategory\Factories\CategoryFactory;
use Hexatex\LaravelCategory\Tests\TestCase;
use Hexatex\LaravelImage\Factories\ImageFactory;
use Hexatex\LaravelImage\Image\Image;
use Hexatex\LaravelMetadata\Factories\MetadataFactory;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use \Mockery;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCategoryTableExists()
    {
        // Act & Assert
        $this->assertTrue(Schema::hasTable('categories'));
    }

    public function testCategoryTableExistsWithCorrectColumns()
    {
        // Act
        $tableColumns = Schema::getColumnListing('categories');

        // Assert
        $expectedColumns = [
            'id',
            'title',
            'page_title',
            'description',
            'sort_by',
            'sort_desc',
            'is_hidden',
            'created_at',
            'updated_at',
            'main_image_id',
            'parent_id',
            'type_id',
            'type_type',
        ];
        foreach ($expectedColumns as $column) {
            $this->assertContains($column, $tableColumns);
        }
    }

    public function testFillableAttributes()
    {
        // Arrange
        $category = new Category();

        // Act
        $fillableAttributes = $category->getFillable();

        // Assert
        $expectedFillable = [
            'title',
            'page_title',
            'description',
            'sort_by',
            'sort_desc',
            'is_hidden',
        ];
        $this->assertEquals($expectedFillable, $fillableAttributes);
    }

    public function testCastsAttributes()
    {
        // Arrange
        $category = new Category();

        // Act
        $castsAttributes = $category->getCasts();

        // Assert
        $expectedCasts = [
            'is_hidden' => 'boolean',
            'sort_desc' => 'boolean',
        ];
        $this->assertEquals($expectedCasts, $castsAttributes);
    }

    public function testTypeRelation()
    {
        // Arrange
        $category = new Category();

        // Act
        $relation = $category->type();

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphTo::class, $relation);
    }

    public function testImagesRelation()
    {
        // Arrange
        $category = new Category();

        // Act
        $relation = $category->images();

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphToMany::class, $relation);
    }

    public function testMetadataRelation()
    {
        // Arrange
        $category = new Category();

        // Act
        $relation = $category->metadata();

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    public function testSlugRelation()
    {
        // Arrange
        $category = new Category();

        // Act
        $relation = $category->slug();

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphOne::class, $relation);
    }

    public function testMainImageRelation()
    {
        // Arrange
        $category = new Category();

        // Act
        $relation = $category->mainImage();

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertInstanceOf(Image::class, $relation->getRelated());
    }

    public function testParentRelation()
    {
        // Arrange
        $category = new Category();

        // Act
        $relation = $category->parent();

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertInstanceOf(Category::class, $relation->getRelated());
    }

    public function testChildrenRelation()
    {
        // Arrange
        $category = new Category();

        // Act
        $relation = $category->children();

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertInstanceOf(Category::class, $relation->getRelated());
        $this->assertEquals('parent_id', $relation->getForeignKeyName());
    }

    public function testItemsRelation()
    {
        // Arrange
        $category = new Category();

        // Act
        $relation = $category->items();

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertInstanceOf(CategoryItem::class, $relation->getRelated());
    }

    public function testGetDefaultSlug()
    {
        // Arrange
        $category = new Category();
        $category->title = 'test title';

        // Act
        $defaultSlug = $category->getDefaultSlug();

        // Assert
        $this->assertEquals('test title', $defaultSlug);
    }

    public function testCreateCategory()
    {
        // Arrange
        $fill = [
            'title' => 'test title',
            'page_title' => 'test page_title',
            'description' => 'test description',
            'sort_by' => 'test sort_by',
            'sort_desc' => false,
            'is_hidden' => false,
        ];
        $category = $this->makeCategory($fill);

        // Act
        $category->save();

        // Assert
        $this->assertNotNull($category);
        $this->assertEquals('test title', $category->title);
        $this->assertEquals('test page_title', $category->page_title);
        $this->assertEquals('test description', $category->description);
        $this->assertEquals('test sort_by', $category->sort_by);
        $this->assertEquals(false, $category->sort_desc);
        $this->assertEquals(false, $category->is_hidden);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function testUpdateCategory()
    {
        // Arrange
        $fill = [
            'title' => 'test title',
            'page_title' => 'test page_title',
            'description' => 'test description',
            'sort_by' => 'test sort_by',
            'sort_desc' => false,
            'is_hidden' => false,
        ];
        $category = $this->makeCategory($fill);
        $category->save();

        $updatedData = [
            'title' => 'updated test title',
            'page_title' => 'updated test page_title',
            'description' => 'updated test description',
            'sort_by' => 'updated test sort_by',
            'sort_desc' => true,
            'is_hidden' => true,
        ];

        // Act
        $category->update($updatedData);

        // Assert
        $this->assertEquals('updated test title', $category->title);
        $this->assertEquals('updated test page_title', $category->page_title);
        $this->assertEquals('updated test description', $category->description);
        $this->assertEquals('updated test sort_by', $category->sort_by);
        $this->assertEquals(true, $category->sort_desc);
        $this->assertEquals(true, $category->is_hidden);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function testDeleteCategory()
    {
        // Arrange
        $fill = [
            'title' => 'test title',
            'page_title' => 'test page_title',
        ];
        $category = $this->makeCategory($fill);
        $category->save();

        // Act
        $category->delete();

        // Assert
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /*
     * Protected Methods
     */
    protected function makeCategory(array $fill = [])
    {
        $metadata = MetadataFactory::new()->create();
        $category = new Category($fill);
        $category->metadata()->associate($metadata);

        return $category;
    }
}
