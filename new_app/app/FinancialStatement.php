<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialStatement extends Model
{
    protected $table = 'financial_statements';
    protected $guarded = ['id'];
    use HasFactory;
}
