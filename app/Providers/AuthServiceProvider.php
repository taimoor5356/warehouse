<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\AdminModels\Category;
use App\AdminModels\Customers;
use App\AdminModels\Inventory;
use App\AdminModels\UpcomingInventory;
use App\AdminModels\OtwInventory;
use App\AdminModels\Labels;
use App\AdminModels\Orders;
use App\AdminModels\Products;
use App\AdminModels\UserRoles;
use App\Models\User;
// use App\Policies\CategoryPolicy;
// use App\Policies\CustomersPolicy;
// use App\Policies\InventoryPolicy;
// use App\Policies\OrdersPolicy;
// use App\Policies\UpcomingInventoryPolicy;
// use App\Policies\OtwInventoryPolicy;
// use App\Policies\LabelsPolicy;
// use App\Policies\ProductsPolicy;
// use App\Policies\UserRolesPolicy;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        // Category::class => CategoryPolicy::class,
        // Customers::class => CustomersPolicy::class,
        // Inventory::class => InventoryPolicy::class,
        // UpcomingInventory::class => UpcomingInventoryPolicy::class,
        // OtwInventory::class => OtwInventoryPolicy::class,
        // Labels::class => LabelsPolicy::class,
        // Products::class => ProductsPolicy::class,
        // UserRoles::class => UserRolesPolicy::class,
        // Orders::class => OrdersPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        
    }
}
