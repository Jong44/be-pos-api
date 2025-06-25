<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Model;

class OpenBillDetail extends Model
{
    use HasUuid;

    protected $fillable = [
        'code',
        'open_bill_id',
        'product_id',
        'price',
        'cost',
        'qty',
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
