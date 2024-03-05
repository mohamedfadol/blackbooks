<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompleteOrder extends Model
{
    use HasFactory;
    protected $table = 'order_complete';

    /**
     * The sell_lines that belong to the.
     */
    public function sell_lines()
    {
        return $this->belongsToMany(\App\TransactionSellLine::class, 'order_complete', 'line_id', 'kitchen_id');
    }

    /**
     * The kitchens that belong to the.
     */
    public function kitchens()
    {
        return $this->belongsToMany(\App\Kitchen::class, 'order_complete', 'kitchen_id', 'line_id');
    }
}