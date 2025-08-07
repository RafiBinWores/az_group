<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrintReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'garment_type',
        'date',
        'print_data',
    ];

    protected $casts = [
        'print_data' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class,);
    }
}
