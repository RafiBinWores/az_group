<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wash extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'garment_type',
        'wash_data',
        'date',
    ];

    protected $casts = [
        'wash_data' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class,);
    }
}
