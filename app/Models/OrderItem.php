<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id','drug_id','quantity','unit_price','line_total'];

    public function order() { return $this->belongsTo(Order::class); }
    public function drug()  { return $this->belongsTo(Drug::class); }
}
