<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id','company_id','status','total_amount'];

    public function items()    { return $this->hasMany(OrderItem::class); }
    public function pharmacy() { return $this->belongsTo(User::class, 'user_id'); }
    public function company()  { return $this->belongsTo(User::class, 'company_id'); }
    public function delivery() { return $this->hasOne(\App\Models\Delivery::class); }
}
