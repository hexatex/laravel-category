<?php

namespace Hexatex\LaravelCategory\Http\Controllers\Admin\Api;

use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelCategory\Category\Contracts\CategoryService;
use Hexatex\LaravelCategory\Category\Requests\Admin\IndexRequest;
use Hexatex\LaravelCategory\Category\Requests\Admin\StoreRequest;
use Hexatex\LaravelCategory\Category\Requests\Admin\UpdateRequest;
use Hexatex\LaravelCategory\Category\Resources\Admin\CategoryResource;
use Hexatex\LaravelMisc\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    protected $indexLoad = [
        'type',
        'mainImage',
        'slug',
    ];

    protected $load = [
        'type',
        'mainImage',
        'parent',
        'children',
        'slug',
        'metadata',
    ];

    public function __construct(CategoryService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $categories = $this->service->index($request->validated());

        $this->indexLoad($categories);

        return CategoryResource::collection($categories);
    }

    public function get(Category $category): CategoryResource
    {
        $this->load($category);

        return new CategoryResource($category);
    }

    public function store(StoreRequest $request): CategoryResource
    {
        $this->db->transaction(function () use ($request, &$category) {
            $category = $this->service->store($request->validated());
        });

        $this->load($category);

        return new CategoryResource($category);
    }

    public function update(UpdateRequest $request, Category $category): CategoryResource
    {
        $this->db->transaction(function () use ($request, $category) {
            $this->service->update($request->validated(), $category);
        });

        $this->load($category);

        return new CategoryResource($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->db->transaction(function () use ($category) {
            $this->service->destroy($category);
        });

        return response()->json();
    }
}
