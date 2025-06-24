<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
     protected $table = 'logs'; 

     public $timestamps = false;

      protected $fillable = [
        'table_name',
        'operation',
        'change_at',
        'data',
        'user_id'
    ];

    protected $hidden = [
      'data'
  ];
}
