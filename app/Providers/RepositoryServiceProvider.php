<?php

namespace App\Providers;

use App\Interfaces\InventoryRepositoryInterface;
use App\Repositories\CategoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\CategoryRepository;
use App\Repositories\product\productInterface;
use App\Repositories\product\productRepository;
use App\Repositories\customer\CustomerInterface;
use App\Repositories\customer\CustomerRepository;
use App\Repositories\InventoryRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(productInterface::class,productRepository::class);
        $this->app->bind(CategoryInterface::class,CategoryRepository::class);
        $this->app->bind(CustomerInterface::class,CustomerRepository::class);
        $this->app->bind(InventoryRepositoryInterface::class,InventoryRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
