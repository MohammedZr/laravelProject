<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id','drug_id','company_id','quantity','unit_price'];

    public function cart()   { return $this->belongsTo(Cart::class); }
    public function drug()   { return $this->belongsTo(Drug::class); }
    public function company(){ return $this->belongsTo(User::class, 'company_id'); }
}
