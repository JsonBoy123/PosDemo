<?php

namespace App\Models\Receivings;

use Illuminate\Database\Eloquent\Model;

class ReceivingItem extends Model
{
    protected $table = 'receivings_items';
    protected $guarded = []; 

    public function item(){
        return $this->belongsTo('App\Models\Item\Item','item_id', 'id')->withdefault(['item' => 0]);
    }

    public function sale(){
        return $this->belongsTo('App\Models\Sales\Sale','sale_id', 'id');
    }

    public function sale_items(){
        return $this->belongsTo('App\Models\Sales\SalesItem','id', 'sale_id');
    }

    public function receivings(){
        return $this->belongsTo(Receiving::class, 'reference_receiving_id', 'id');
    }

    public function requested_items(){
        return $this->hasMany(ReceivingRequestItems::class, 'receiving_request_id')->withdefault(['quantity' => 0]);
    }
}
