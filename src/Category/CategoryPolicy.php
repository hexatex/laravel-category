<?php

namespace Hexatex\LaravelCategory\Category;

use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelMisc\Contracts\Authenticatable;

class CategoryPolicy
{
    protected $user;
    protected $category;

    public function __construct(?Authenticatable $user, Category $category)
    {
        $this->user = $user;
        $this->category = $category;
    }

    public function view(): bool
    {
        if (!$this->category->is_hidden) {
            return true;
        }

        if ($this->user && $this->user->hasPermissionTo('category.view-hidden')) {
            return true;
        }

        return false; // Category is hidden and the Authenticatable (user) does not have permission
    }

    public function update(): bool
    {
        return $this->checkPermission('update');
    }

    public function destroy(): bool
    {
        return $this->checkPermission('destroy');
    }

    protected function checkPermission(string $permission): bool
    {
        if (!$this->user) {
            return false;
        }

        if ($this->category->is_hidden && $this->user->hasPermissionTo("category.{$permission}-hidden")) {
            return true;
        }

        if ($this->category->is_hidden) {
            return false;
        }

        return $this->user->hasPermissionTo("category.{$permission}");
    }
}
