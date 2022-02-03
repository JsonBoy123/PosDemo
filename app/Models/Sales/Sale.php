<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $guarded = [];

    public function item(){   
        return $this->belongsTo('App\Models\Item\Item','item_id', 'id')->withdefault(['item' => 0]);
    }

    public function saleItems(){
        return $this->hasMany('App\Models\Sales\SalesItem', 'category_id');
    }

    public function sales_half_payment(){
        return $this->belongsTo('App\Models\Item\item_Discount', 'id');
    }

    public function item_discount(){
        return $this->belongsTo('App\Models\item\item_Discount', 'id');   
    }

    public function it_dic()
    {
        return $this->hasMany('App\Models\item\Item_Discount', 'id','id'); 
    }

    public function sale_payment(){
        return $this->belongsTo('App\Models\Sales\SalesPayment','id','sale_id')->withdefault(['payment_amount' => 0]);
    }

    public function customer(){
        return $this->belongsTo('App\Customer','customer_id','id');
    }

    public function shop(){
        return $this->belongsTo('App\Models\Office\Shop\Shop','employee_id','id');
    }

    public function cashier(){
        return $this->belongsTo('App\Models\Manager\ControlPanel\Cashier','cashier_id','id');
    }

    public function half_payment(){
        return $this->belongsTo('App\Models\Sales\HalfPayment','id','sale_id');
    }

    public function discount_amt(){
        return $this->belongsTo('App\Models\Sales\OtherDiscount','id','sale_id');
    }

    /*public function customers(){
        return $this->hasMany('App\Customer','customer_id','id');
    }*/

    public function sale_items(){
        return $this->belongsTo('App\Models\Sales\SalesItem','id', 'sale_id')->withdefault(['item_unit_price' => 0]);
    }

    public function employee(){
        return $this->belongsTo('App\Models\Office\Employees\Employees', 'employee_id');
    }

    public function employee_shop(){
        return $this->belongsTo('App\Models\Office\Shop\Shop', 'employee_id', 'id');
    }

    public function customer_due(){
        return $this->belongsTo('App\Models\Office\wholesaleCustomer\WholesaleCustomer', 'id', 'sale_id');
    }

    public function sales_taxes()
    {
        return $this->belongsTo('App\Models\Sales\SaleTaxe','id', 'sale_id')->withdefault(['sale_tax_basis' => 0]);
    }
    
}