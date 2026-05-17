<?php

namespace Hexatex\LaravelCategory\Tests\Unit\Category;

use Hexatex\LaravelCategory\Category\CategoryPolicy;
use Hexatex\LaravelCategory\Category\Contracts\Category;
use Hexatex\LaravelCategory\Factories\CategoryFactory;
use Hexatex\LaravelCategory\Tests\TestCase;
use Hexatex\LaravelMisc\Contracts\Authenticatable;
use Hexatex\LaravelUser\Factories\UserFactory;
use Hexatex\LaravelUser\User\Contracts\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \Mockery;

class CategoryPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $notHiddenCategory;
    protected $hiddenCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->make();
        $this->user = Mockery::mock($this->user)->makePartial();
        $this->user->id = 1000;

        $this->notHiddenCategory = CategoryFactory::new()->make();
        $this->notHiddenCategory = Mockery::mock($this->notHiddenCategory)->makePartial();

        $this->hiddenCategory = CategoryFactory::new()->make();
        $this->hiddenCategory->is_hidden = true;
        $this->hiddenCategory = Mockery::mock($this->hiddenCategory)->makePartial();
    }

    public function testView_withUser_notHidden()
    {
        // Arrange
        $policy = new CategoryPolicy($this->user, $this->notHiddenCategory);

        // Act
        $this->user->shouldNotReceive('hasPermissionTo'); // no permission needed if it is not hidden

        $result = $policy->view();

        // Assert
        $this->assertTrue($result);
    }

    public function testView_withUser_isHidden_hasHiddenPermission()
    {
        // Arrange
        $policy = new CategoryPolicy($this->user, $this->hiddenCategory);

        // Act
        $this->shouldAllowPermissionTo('category.view-hidden', true);

        $result = $policy->view();

        // Assert
        $this->assertTrue($result);
    }

    public function testView_withUser_isHidden_noHiddenPermission()
    {
        // Arrange
        $policy = new CategoryPolicy($this->user, $this->hiddenCategory);

        // Act
        $this->shouldAllowPermissionTo('category.view-hidden', false);

        $result = $policy->view();

        // Assert
        $this->assertFalse($result);
    }

    public function testView_withUser_isHidden()
    {
        // Arrange
        $policy = new CategoryPolicy(null, $this->hiddenCategory);

        // Act
        $result = $policy->view();

        // Assert
        $this->assertFalse($result);
    }

    public function testView_withoutUser_notHidden()
    {
        // Arrange
        $policy = new CategoryPolicy(null, $this->notHiddenCategory);

        // Act
        $result = $policy->view();

        // Assert
        $this->assertTrue($result);
    }

    public function testUpdate_withoutUser()
    {
        $this->testUpdateOrDestroy_withoutUser('update');
    }

    public function testUpdate_hasPermission_isHidden_noHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'update',
            hasPermission: true,
            isHidden: true,
            hasHiddenPermission: false,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testUpdate_hasPermission_isHidden_hasHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'update',
            hasPermission: true,
            isHidden: true,
            hasHiddenPermission: true,
        );

        // Assert
        $this->assertTrue($result);
    }

    public function testUpdate_hasPermission_notHidden_hasHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'update',
            hasPermission: true,
            isHidden: false,
            hasHiddenPermission: true,
        );

        // Assert
        $this->assertTrue($result);
    }

    public function testUpdate_hasPermission_notHidden_noHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'update',
            hasPermission: true,
            isHidden: false,
            hasHiddenPermission: false,
        );

        // Assert
        $this->assertTrue($result);
    }

    public function testUpdate_noPermission_notHidden_noHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'update',
            hasPermission: false,
            isHidden: false,
            hasHiddenPermission: false,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testUpdate_noPermission_notHidden_hasHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'update',
            hasPermission: false,
            isHidden: false,
            hasHiddenPermission: true,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testUpdate_noPermission_isHidden_noHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'update',
            hasPermission: false,
            isHidden: true,
            hasHiddenPermission: false,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testUpdate_noPermission_isHidden_hasHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'update',
            hasPermission: false,
            isHidden: true,
            hasHiddenPermission: true,
        );

        // Assert
        $this->assertTrue($result);
    }

    public function testDestroy_withoutUser()
    {
        $this->testUpdateOrDestroy_withoutUser('destroy');
    }

    public function testDestroy_hasPermission_isHidden_noHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'destroy',
            hasPermission: true,
            isHidden: true,
            hasHiddenPermission: false,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testDestroy_hasPermission_isHidden_hasHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'destroy',
            hasPermission: true,
            isHidden: true,
            hasHiddenPermission: true,
        );

        // Assert
        $this->assertTrue($result);
    }

    public function testDestroy_hasPermission_notHidden_hasHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'destroy',
            hasPermission: true,
            isHidden: false,
            hasHiddenPermission: true,
        );

        // Assert
        $this->assertTrue($result);
    }

    public function testDestroy_hasPermission_notHidden_noHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'destroy',
            hasPermission: true,
            isHidden: false,
            hasHiddenPermission: false,
        );

        // Assert
        $this->assertTrue($result);
    }

    public function testDestroy_noPermission_notHidden_noHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'destroy',
            hasPermission: false,
            isHidden: false,
            hasHiddenPermission: false,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testDestroy_noPermission_notHidden_hasHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'destroy',
            hasPermission: false,
            isHidden: false,
            hasHiddenPermission: true,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testDestroy_noPermission_isHidden_noHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'destroy',
            hasPermission: false,
            isHidden: true,
            hasHiddenPermission: false,
        );

        // Assert
        $this->assertFalse($result);
    }

    public function testDestroy_noPermission_isHidden_hasHiddenPermission()
    {
        // Arrange and Act
        $result = $this->testUpdateOrDestroy(
            permission: 'destroy',
            hasPermission: false,
            isHidden: true,
            hasHiddenPermission: true,
        );

        // Assert
        $this->assertTrue($result);
    }

    /*
     * Protected Methods
     */
    protected function testUpdateOrDestroy_withoutUser(string $permission)
    {
        // Arrange
        $policy = new CategoryPolicy(null, $this->notHiddenCategory);
        $policy = Mockery::mock($policy)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        // Act
        $policy->shouldReceive($permission)
            ->once()
            ->passthru();

        $policy->shouldReceive('checkPermission')
            ->with($permission)
            ->once()
            ->passthru();

        $policy->shouldNotReceive('notHiddenOrAllowed');

        $result = $policy->{$permission}();

        // Assert
        $this->assertFalse($result);
    }

    protected function testUpdateOrDestroy(
        string $permission,
        bool $hasPermission,
        bool $isHidden,
        bool $hasHiddenPermission
    ) {
        // Arrange
        $category = $isHidden ? $this->hiddenCategory : $this->notHiddenCategory;
        $policy = Mockery::mock(CategoryPolicy::class, [$this->user, $category])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        // Act
        $policy
            ->shouldReceive($permission)
            ->once()
            ->passthru();

        $policy
            ->shouldReceive('checkPermission')
            ->with($permission)
            ->once()
            ->passthru();

        if ($isHidden) {
            $this->shouldAllowPermissionTo("category.{$permission}-hidden", $hasHiddenPermission);

            if ($hasHiddenPermission) {
                $this->user
                    ->shouldNotReceive('hasPermissionTo')
                    ->with("category.{$permission}");
            } else {
                $this->shouldAllowPermissionTo("category.{$permission}", $hasPermission);
            }
        } else {
            $this->shouldAllowPermissionTo("category.{$permission}", $hasPermission);
        }

        $result = $policy->{$permission}();

        return $result;
    }

    protected function shouldAllowPermissionTo(string $permission, bool $allow)
    {
        $this->user
            ->shouldReceive('hasPermissionTo')
            ->with($permission)
            ->andReturn($allow);
    }
}
