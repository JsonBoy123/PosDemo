<?php

namespace App\Models\Item;

use Illuminate\Database\Eloquent\Model;
use Auth;
class Item extends Model
{
    protected $guarded = [];
    protected $table = 'items';

    public function ItemTax() {
        return $this->belongsTo('App\Models\item\items_taxes', 'id','item_id');
    }

    public function Sale(){
        return $this->belongsTo('App\Models\Sales\Sale','sale_id', 'id');
    }

    public function sale_items(){
        return $this->belongsTo('App\Models\Sales\SalesItem','sale_id', 'id');
    }

    public function brandName(){
        return $this->belongsTo('App\Models\Manager\MciBrand', 'brand');
    }

    public function colorName(){
        return $this->belongsTo('App\Models\Manager\MciColor', 'color');
    }

    public function categoryName(){
        return $this->belongsTo('App\Models\Manager\MciCategory', 'category')->withdefault(['category_name' => ' ']);
    }

    public function subcategoryName(){
        return $this->belongsTo('App\Models\Manager\MciSubCategory', 'subcategory')->withdefault(['sub_categories_name' => ' ']);
    }

    public function sizeName(){
        return $this->belongsTo('App\Models\Manager\MciSize', 'size');
    }

    public function item_quantity(){
        return $this->belongsTo('App\Models\Item\item_quantities','id','item_id');
    }

    public function item_discount(){
        return $this->belongsTo('App\Models\Item\Item_Discount','id','item_id');
    }

    public function item_quantities(){
        return $this->hasMany('App\Models\Item\item_quantities','item_id', 'id');
    }

    public function quantities(){
        return $this->hasMany(item_quantities::class, 'item_id', 'id');
    }

    public function receiving_items(){
        return $this->belongsTo('App\Models\Receivings\ReceivingItem','receiving_id', 'id');
    }

    public function receivings(){
        return $this->belongsTo(Receiving::class, 'reference_receiving_id', 'id');
    }

    public function receiving_request(){
        return $this->belongsTo('App\Models\Receivings\ReceivingRequest','receiving_id', 'id');
    }   
}
