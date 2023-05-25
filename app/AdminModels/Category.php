<?php

namespace App\AdminModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\AdminModels\Products;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $table = 'category';
    public $timestamps = false;
    protected $fillable = ['id', 'name'];


    public static function addcategory($insArr)
  	{
  		DB::table('category')->insert($insArr);
  	}

    public function Products()
    {
        return $this->hasMany(Products::class);
    }

    public function product()
    {
        return $this->hasMany(Products::class, 'category_id', 'id');
    }

    public static function updatecategory($data, $id = 0)
    {
      $affected = DB::table('category')
            ->where('id', $id)
            ->update($data);
    }

    public static function delete_category($id)
  	{
      Category::where('id',$id)->delete();

  	}


}
