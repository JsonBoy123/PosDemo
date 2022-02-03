<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class SalesPayment extends Model
{
    protected $table = 'sales_payments';
    protected $guarded = [];

    public function sale(){
    	return $this->belongsTo('App\Models\Sales\Sale', 'sale_id');
    }

    public function saleItems(){
        return $this->hasMany('App\Models\Sales\SalesItem', 'category_id');
    }

    public function shop(){
        return $this->belongsTo('App\Models\Office\Shop\Shop','employee_id','id');
    }

    public function sale_items(){
        return $this->belongsTo('App\Models\Sales\SalesItem','id', 'sale_id');
    }
}
