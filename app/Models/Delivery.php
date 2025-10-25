<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'order_id','courier_id','status','assigned_at','picked_up_at','delivered_at','failed_reason'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order(){ return $this->belongsTo(Order::class); }
    public function courier(){ return $this->belongsTo(User::class, 'courier_id'); }
}
