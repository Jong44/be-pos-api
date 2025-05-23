<?php

namespace App\Models;

use App\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'outlet_id',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
