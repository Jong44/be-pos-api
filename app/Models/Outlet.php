<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasUuid;

    protected $fillable = [
        'outlet_name',
        'address',
        'phone_number',
        'email',
        'latitude',
        'longitude',
        'tax',
    ];
}
