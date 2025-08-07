<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Embroidery extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'garment_type',
        'date',
        'embroidery_data',
    ];

    protected $casts = [
        'embroidery_data' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class,);
    }
}
