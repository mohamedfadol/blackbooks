<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LineDetails extends Model
{
    use HasFactory;
    protected $table = 'order_status';
    protected $guarded = ['id'];
    /**
     * Get the line that owns the LineDetails
     *
     * @return BelongsTo 
     */
    // public function line(): BelongsTo
    // {
    //     return $this->belongsTo(\App\TransactionSellLine::class, 'line');
    // }

    /**
     * Get the transaction that owns the LineDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id');
    }

    /**
     * Get the kitchen that owns the LineDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kitchen(): BelongsTo
    {
        return $this->belongsTo(\App\Kitchen::class, 'kitchen_id');
    }

}
