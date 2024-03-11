<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountCategory extends Model
{
    protected $table = 'account_categories';
    protected $guarded = ['id'];
    use HasFactory;
}
