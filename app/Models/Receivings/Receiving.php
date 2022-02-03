<?php

namespace App\Models\Receivings;

use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{ 
    protected $table = 'receivings';
    protected $guarded = [];
    
    public function stock_movement(){
    	return $this->belongsTo('App\Models\Stock\StockMovent','receiving_id');
    }

    public function item(){
        return $this->belongsTo('App\Models\Item\Item','item_id');
    }

    public function receiving_items(){
    	return $this->belongsTo('App\Models\Receivings\ReceivingItem','receiving_id');
    }

    public function receiving_request(){
        return $this->belongsTo('App\Models\Receivings\ReceivingRequest','id');
    }

    public function requested_items(){
        return $this->hasMany(ReceivingRequestItems::class, 'receiving_request_id')->withdefault(['quantity' => 0]);
    }

    public function repair_amount(){
        return $this->hasMany('App\Models\Repair\RepairComplete','rec_id');
    }

    public function shopName(){
    	return $this->hasMany('App\Models\Office\Employees\Employees', 'employee_id');
    }

    public function cashier(){
        return $this->belongsTo('App\Models\Manager\ControlPanel\Cashier','cashier_id','id')->withdefault(['name' => ' ']);
    }

    public function repair_quantity(){
        return $this->hasMany('App\Models\Repair\RepairComplete', 'rec_id');
    }
    public function stocks_transfer(){
        return $this->hasMany('App\Models\Stock\StockTransfer','receiving_id');
    }
    public function sale_items(){
        return $this->belongsTo('App\Models\Sales\SalesItem','id', 'sale_id');
    }
}
