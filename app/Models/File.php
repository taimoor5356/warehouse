<?php

namespace App\Models;

use App\AdminModels\Customers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function customer() {
        return $this->belongsTo(Customers::class);
    }
}
