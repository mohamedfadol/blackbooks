<?php

namespace Database\Factories;

use App\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'business_id' => 1,
            'location_id' => 1,
            'type' => 'sell',
            'status' => 'final',
            'is_quotation' => 0,
            'payment_status' => 'paid',
            'contact_id' => 1,
            'invoice_no' => '0001',
            'transaction_date' => now(),
            'total_before_tax' => '1000',
            'tax_amount' => '0.0000',
            'discount_type' => 'percentage',
            'final_total' => '1000',
            'created_by' => 1,
            'mfg_production_cost_type' => 'percentage',
            'recur_interval_type' => 'days',
            'recur_interval' => '1.0000',
            'selling_price_group_id' => '0'
        ];
    }
}
