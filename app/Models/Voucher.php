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

    public function applyable($code, $price)
    {
        $today = now();
        $voucher = Voucher::where('code', $code)
            ->where('status', 'active')
            ->where('start_date', '<=', $today)
            ->where('expired_date', '>=', $today)
            ->first();

        if ($voucher) {
            if ($voucher->minimum_buying > $price || $voucher->status != 'active') {
                return false;
            }
            return true;
        }
        return false;


    }


}
