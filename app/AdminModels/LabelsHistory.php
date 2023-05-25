<?php

namespace App\AdminModels;

use App\AdminModels\Labels;
use App\AdminModels\Customers;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabelsHistory extends Model
{
	use Notifiable,SoftDeletes;
    
    protected $table = 'labels_history';
    protected $protected = [];
    protected $guarded = [];
    

    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }

    public function brand(){
        return $this->belongsTo(Labels::class);
    }

    
}
