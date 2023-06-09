<?php

namespace Hexatex\LaravelCategory\Category;

use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\JoinClause;
use SimpleXMLElement;

class CategoryService
{
    /** @var string[] */
    private $categoryTypes = [];

    /** @var CategoryTypeService[] */
    private $categoryTypeServices = [];

    /**
     * @param  string[]  $categoryTypes
     */
    public function setCategoryTypes(array $categoryTypes): void
    {
        $this->categoryTypes = $categoryTypes;
        // Todo: use this array to call store, update destroy on the category type services from this services store update destroy methods
    }

    /**
     * @return Category[]|LengthAwarePaginator
     */
    public function index(array $filters, Category $parent = null): LengthAwarePaginator
    {
        return Category::filter($filters)
            ->byParent($parent)
            ->paginate($filters['rowsPerPage'] ?? config('category.max-rows-per-page'));
    }

    /**
     * Return array of Category recursive hierarchy
     *
     * @return array [[id, name],]
     */
    public function indexHierarchy(string $search): array
    {
        $fills = DB::select('select rid, rxmlpath from category_index_hierarchy(?)', [$search]);

        $categories = [];
        foreach ($fills as $fill) {
            $xmlPath = new SimpleXMLElement($fill->rxmlpath);

            foreach ($xmlPath->children() as $child) {
                $categoryId = (int) $child->id;
                $categories[$categoryId] = $categories[$categoryId]
                    ?? ['id' => $categoryId, 'name' => (string) $child->name];
            }
        }

        return $categories;
    }

    public function store(array $fill): Category
    {
        $categoryType = $this->categoryTypeService($category)->store($fill['categoryType']);

        $category = new Category($fill);
        $category->categoryType()->associate($categoryType);
        $category->menuImage()->associate($fill['menu_image']['id'] ?? null);
        $category->save();

        $menuIds = collect($fill['menus'])->pluck('id');
        $category->menus()->attach($menuIds);

        return $category;
    }

    public function update(array $fill, Category $category): void
    {
        $category->fill($fill);
        $category->menuImage()->associate($fill['menu_image']['id'] ?? null);
        $category->save();

        $this->categoryTypeService($category)->update($fill['categoryType'], $category->categoryType);

        $menuIds = collect($fill['menus'])->pluck('id');
        $category->menus()->sync($menuIds);
    }

    /**
     * Destroy
     *
     * @throws \Exception
     */
    public function destroy(Category $category): void
    {
        $category->delete();

        $this->categoryTypeService($category)->destroy($category->categoryType);
    }

    public function addProducts(array $fills, Category $category): void
    {
        $lowestDisplayOrder = $category->products()->min('category_product.display_order') ?: 0;
        $displayOrder = $lowestDisplayOrder - count($fills);
        foreach ($fills as $productFill) {
            $category->products()->attach($productFill['id'], ['display_order' => $displayOrder]);
            $displayOrder++;
        }
    }

    public function removeProducts(array $fill, Category $category): void
    {
        $productIds = collect($fill)->pluck('id');
        $category->products()->detach($productIds);
    }

    /**
     * Reorder display_order on Product
     */
    public function reorderProduct(Category $category, Product $product, bool $before, Product $neighbor): void
    {
        // Give $product $neighbors display_order
        DB::table('category_product as a')
            ->join('category_product as b', function (JoinClause $join) use ($neighbor) {
                $join->where('b.product_id', $neighbor->id);
            })
            ->where('a.product_id', $product->id)
            ->where('a.category_id', $category->id)
            ->where('b.category_id', $category->id)
            ->update(['a.display_order' => DB::raw('b.display_order')]);

        // Move the rows before or after $product new position
        if ($before) {
            \DB::table('category_product as a')
                ->join('category_product as b', function (JoinClause $join) use ($neighbor) {
                    $join->where('b.product_id', $neighbor->id);
                })
                ->where('a.category_id', $category->id)
                ->where('b.category_id', $category->id)
                ->where('a.product_id', '!=', $product->id)
                ->where('a.display_order', '>=', DB::raw('b.display_order'))
                ->update(['a.display_order' => DB::raw('a.display_order + 1')]);
        } else { // after
            \DB::table('category_product as a')
                ->join('category_product as b', function (JoinClause $join) use ($neighbor) {
                    $join->where('b.product_id', $neighbor->id);
                })
                ->where('a.category_id', $category->id)
                ->where('b.category_id', $category->id)
                ->where('a.product_id', '!=', $product->id)
                ->where('a.display_order', '<=', DB::raw('b.display_order'))
                ->update(['a.display_order' => DB::raw('a.display_order - 1')]);
        }
    }

   /*
     * Private Methods
     */
   /**
    * Get the service for the category type, configure in LaravelCategoryServiceProvider with LaravelCategory::setCategoryTypes
    */
   private function categoryTypeService(Category $category): CategoryTypeService
   {
       $serviceClass = $this->categoryTypes[$category->category_type_type];

       if (empty($this->categoryTypeServices[$serviceClass])) {
           $this->categoryTypeServices[$serviceClass] = app($serviceClass);
       }

       return $this->categoryTypeServices[$serviceClass];
   }
}
