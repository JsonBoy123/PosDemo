<?php

namespace App\Models\Manager;

use Illuminate\Database\Eloquent\Model;

class MciCategory extends Model
{
    protected $guarded = [];
    protected $table = 'mci_categories';

    public function saleItems(){
        return $this->hasMany('App\Models\Sales\SalesItem', 'category_id');
    }

    //Get categories on specific date
    public function scopeGetCategories($query, $items){

        return $query->whereIn('id', $items)->pluck('category_name', 'id');
    }

    public function item_discount(){
        return $this->hasMany('App\Models\Item\Item_Discount','id','item_id');
    }

     public function item(){
        return $this->belongsTo('App\Models\Item\Item','category','id');
    }

}