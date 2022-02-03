<?php

namespace App\Models\Manager;

use Illuminate\Database\Eloquent\Model;

class MciSubCategory extends Model
{
    protected $guarded = [];
    protected $table = 'mci_sub_categories';

    public function categoryName(){
    	return $this->belongsTo('App\Models\Manager\MciCategory', 'parent_id', 'id');
    }

   public function salescat(){
        return $this->hasManyThrough('App\Models\Sales\Sale','App\Models\Sales\SalesItem','category_id','id','id','sale_id');
    }

    public function item(){
        return $this->belongsTo('App\Models\Item\Item','subcategory', 'id');
    }
}
