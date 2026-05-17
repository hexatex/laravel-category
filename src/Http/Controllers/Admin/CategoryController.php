<?php

namespace Hexatex\LaravelCategory\Http\Controllers\Admin;

use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelCategory\Category\Contracts\CategoryService;
use Hexatex\LaravelCategory\Requests\Admin\IndexRequest;
use Hexatex\LaravelMisc\Controller;
use Illuminate\Contracts\View\View;
use \Storage;

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
    ];

    public function __construct(CategoryService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    public function index(IndexRequest $request): View
    {
        $categories = $this->categoryService->index($request->validated());

        $this->indexLoad($categories);

        return view('admin.category.index', compact('categories'));
    }

    public function show(Category $category): View
    {
        $this->load($category);

        return view('admin.category.show', compact('category'));
    }
}
