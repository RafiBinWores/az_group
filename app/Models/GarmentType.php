<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GarmentType extends Model
{
    protected $fillable = ['name', 'status'];


    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
