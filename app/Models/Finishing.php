<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Finishing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_id',
        'thread_cutting',
        'qc_check',
        'button_rivet_attach',
        'iron',
        'hangtag',
        'poly',
        'carton',
        'today_finishing',
        'total_finishing',
        'plan_to_complete',
        'dpi_inline',
        'fri_final',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
