<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kitchen extends Model
{
    use HasFactory;
    protected $table = 'kitchens';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public function category() { 
        return $this->belongsTo(\App\Category::class);
    }

    public function lineDetails($transaction_id) 
    { 
        return $this->hasOne(\App\LineDetails::class, 'kitchen_id')->where('transaction_id',$transaction_id)->first();
    }

    public function sell_lines() {
        return $this->hasMany(\App\TransactionSellLine::class, 'kitchen_id');    
    }

    /**
     * The kitchens that belong to the.
     */
    public function complete_orders()
    {
        return $this->belongsToMany(\App\TransactionSellLine::class, 'order_complete', 'kitchen_id', 'line_id')->withPivot('status')->withTimestamps();
    }

    /**
     * Return list of types of service for a business
     *
     * @param  int  $business_id
     * @return array
     */
    public static function forDropdown($business_id)
    {
        $types_of_service = \App\Kitchen::where('business_id', $business_id)
                    ->pluck('name', 'id');

        return $types_of_service;
    }
}
