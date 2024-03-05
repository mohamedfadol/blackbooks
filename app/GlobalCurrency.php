<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalCurrency extends Model
{
    use HasFactory;

    protected $table = 'global_currencies';
    protected $guarded = ['id'];
}
