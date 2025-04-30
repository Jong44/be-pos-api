<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasUuid;

    protected $fillable = [
        'outlet_id',
        'name',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
