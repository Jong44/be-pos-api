<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasUuid;
    protected $fillable = [
        'user_id',
        'outlet_id',
        'latitude',
        'longitude',
        'status',
        'check_in',
        'check_out',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

}
