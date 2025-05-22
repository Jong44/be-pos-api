<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasUuid;
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
