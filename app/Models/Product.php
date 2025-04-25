<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuid;

    protected $fillable = [
        'id',
        'outlet_id',
        'category_id',
        'name',
        'stock',
        'is_non_stock',
        'initial_price',
        'price',
        'unit',
        'hero_images'
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
