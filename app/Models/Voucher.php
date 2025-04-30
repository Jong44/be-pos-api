<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'outlet_id',
        'code',
        'name',
        'type',
        'nominal',
        'start_date',
        'expired_date',
        'minimum_buying',
        'status',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
