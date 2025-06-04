<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Model;

class OpenBill extends Model
{
    use HasUuid;

    protected $fillable = [
        'outlet_id',
        'code',
        'cashier_id',
        'customer_name',
        'open_at',
        'closed_at',
        'status',
        'voucher_id',
        'discount_price',
        'total_qty',
        'total_price',
    ];

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function details()
    {
        return $this->hasMany(OpenBillDetail::class, 'open_bill_id');
    }

    public static function generateCustomCode($prefix = 'BILL', $column = 'code', $padLength = 3)
    {
        $last = self::where($column, 'like', $prefix . '%')
            ->orderBy($column, 'desc')
            ->first();

        if (!$last) {
            $nextNumber = 1;
        } else {
            // Extract numeric part
            $number = (int) substr($last->$column, strlen($prefix));
            $nextNumber = $number + 1;
        }

        return $prefix . str_pad($nextNumber, $padLength, '0', STR_PAD_LEFT);
    }
}
