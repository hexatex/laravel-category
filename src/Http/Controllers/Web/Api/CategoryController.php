<?php

namespace Hexatex\LaravelCategory\Http\Controllers\Web\Api;

use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelCategory\Category\Contracts\CategoryService;
use Hexatex\LaravelCategory\Category\Requests\Web\IndexRequest;
use Hexatex\LaravelCategory\Category\Resources\Web\CategoryResource;
use Hexatex\LaravelMisc\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    protected $indexLoad = [
        'type',
        'mainImage',
    ];

    protected $load = [
        'type',
        'mainImage',
        'parent',
        'children', // todo is this actually going to be used in the web front end?
        'slug',
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
}
