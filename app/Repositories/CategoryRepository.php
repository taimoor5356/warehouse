<?php

namespace App\Repositories;

use App\Models\CustomerUser;
use Illuminate\Http\Request;
use App\AdminModels\Category;
use App\AdminModels\Products;
use App\Models\CustomerProduct;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CategoryInterface;
use DataTables;

class CategoryRepository implements CategoryInterface
{
    //
    public function category($request)
    {
    }
}
