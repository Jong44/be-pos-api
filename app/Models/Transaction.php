<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    use HasUuid;

    protected $fillable = [
        'cashier_id',
        'outlet_id',
        'date',
        'note',
        'voucher_id',
        'discout_price',
        'code',
        'payed_money',
        'money_changes',
        'total_price',
        'total_cost',
        'payment_method_id',
        'tax',
        'tax_price',
        'total_qty',
    ];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public static function generateCustomCode($prefix = 'SEL', $column = 'code', $padLength = 3)
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
