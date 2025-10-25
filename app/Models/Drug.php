<?php

// app/Models/Drug.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
   protected $fillable = [
  'user_id','drug_group_id','name','generic_name','dosage_form','strength',
  'pack_size','unit','sku','barcode','price','stock','is_active','image_url',
];


    public function user()      { return $this->belongsTo(User::class); }
    public function group()     { return $this->belongsTo(DrugGroup::class, 'drug_group_id'); }
}
