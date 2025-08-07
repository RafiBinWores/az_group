<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Production extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'cutting_id',
        'embroidery_id',
        'print_id',
        'garment_type',
        'production_data',
        'date'
    ];

    protected $casts = [
        'production_data' => 'array',
    ];

    // Each production belongs to one order
    public function orders()
    {
        return $this->belongsTo(Order::class);
    }

    // All cuttings for this order and garment type
    public function cuttings()
    {
        return $this->hasMany(Cutting::class)
            ->where('garment_type', $this->garment_type);
    }

    // All embroidery prints for this order and garment type
    public function embroideryPrints()
    {
        return $this->hasMany(Embroidery::class)
            ->where('garment_type', $this->garment_type);
    }

    // All washes for this order and garment type
    public function prints()
    {
        return $this->hasMany(PrintReport::class)
            ->where('garment_type', $this->garment_type);
    }
}
