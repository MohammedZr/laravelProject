<?php

// app/Models/DrugGroup.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugGroup extends Model
{
    protected $fillable = [
        'user_id','title','status','notes','submitted_at','published_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function user()  { return $this->belongsTo(User::class); }
    public function drugs() { return $this->hasMany(Drug::class); }

    public function scopeOwnedBy($q, $userId) { return $q->where('user_id', $userId); }
}
