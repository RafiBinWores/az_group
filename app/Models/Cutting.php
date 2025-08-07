<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cutting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'garment_type',
        'cutting',
        'date'
    ];

    protected $casts = [
        'cutting' => 'array',
    ];

    protected $dates = ['deleted_at'];

    // CUtting belong to order table
    public function order()
    {
        return $this->belongsTo(Order::class,);
    }
}
