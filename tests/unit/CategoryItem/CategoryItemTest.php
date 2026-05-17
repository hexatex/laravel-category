<?php

namespace Hexatex\LaravelCategory\Tests\Unit\CategoryItem;

use Hexatex\LaravelCategory\CategoryItem\CategoryItem;
use Hexatex\LaravelCategory\CategoryItemgedItem\CategoryItemgedItem;
use Hexatex\LaravelCategory\Category\Category;
use Hexatex\LaravelCategory\Factories\CategoryItemFactory;
use Hexatex\LaravelCategory\Tests\TestCase;
use Hexatex\LaravelCategory\Tests\TestObjects\TestCategorizable;
use Hexatex\LaravelImage\Image\Image;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use \Mockery;

class CategoryItemTest extends TestCase
{
    use RefreshDatabase;

    public function testCategoryItemTableExists()
    {
        // Act & Assert
        $this->assertTrue(Schema::hasTable('category_items'));
    }

    public function testCategoryItemTableExistsWithCorrectColumns()
    {
        // Act
        $tableColumns = Schema::getColumnListing('category_items');

        // Assert
        $expectedColumns = ['id', 'created_at', 'updated_at', 'category_id', 'categorizable_id', 'categorizable_type'];
        foreach ($expectedColumns as $column) {
            $this->assertContains($column, $tableColumns);
        }
    }

    public function testFillableAttributes()
    {
        // Arrange
        $categoryItem = new CategoryItem();

        // Act
        $fillableAttributes = $categoryItem->getFillable();

        // Assert
        $expectedFillable = [];
        $this->assertEquals($expectedFillable, $fillableAttributes);
    }

    public function testCategoryRelation()
    {
        // Arrange
        $categoryItem = new CategoryItem();

        // Act
        $relation = $categoryItem->category();

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertInstanceOf(Category::class, $relation->getRelated());
    }

    public function testCategorizableRelation()
    {
        // Arrange
        $categoryItem = new CategoryItem();

        // Act
        $relation = $categoryItem->categorizable();

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphTo::class, $relation);
    }

    public function testCreateCategoryItem()
    {
        // Arrange
        $fill = [
        ];
        $categoryItem = $this->makeCategoryItem($fill);

        // Act
        $categoryItem->save();

        // Assert
        $this->assertNotNull($categoryItem);
        $this->assertInstanceOf(CategoryItem::class, $categoryItem);
        $this->assertDatabaseHas('category_items', ['id' => $categoryItem->id]);
    }

    public function testUpdateCategoryItem()
    {
        // Arrange
        $fill =[
        ];
        $categoryItem = $this->makeCategoryItem($fill);
        $categoryItem->save();

        $updatedData = [
        ];

        // Act
        $categoryItem->update($updatedData);

        // Assert
        $this->assertDatabaseHas('category_items', ['id' => $categoryItem->id]);
    }

    public function testDeleteCategoryItem()
    {
        // Arrange
        $fill = [
        ];
        $categoryItem = $this->makeCategoryItem($fill);
        $categoryItem->save();

        // Act
        $categoryItem->delete();

        // Assert
        $this->assertDatabaseMissing('category_items', ['id' => $categoryItem->id]);
    }

    /*
     * Protected Methods
     */
    protected function makeCategoryItem(array $fill = [])
    {
        $category = new Category;
        $category->id = 12345;
        $testCategorizable = new TestCategorizable;
        $testCategorizable->id = 12345;
        $categoryItem = new CategoryItem($fill);
        $categoryItem->categorizable()->associate($testCategorizable);
        $categoryItem->category()->associate($category);

        return $categoryItem;
    }
}
