<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\TransactionSellLine;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class TransactionSellLineFactory extends Factory
{
    /** 
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
 
     protected $model = TransactionSellLine::class;
    public function definition()
    {
        return [
            'transaction_id' => 1,
            'product_id' => 1,
            'variation_id' => 1,
            'quantity' => '4',
            'unit_price_before_discount' => '25.0000',
            'unit_price' => '25.0',
            'line_discount_type' => 'fixed',
            'unit_price_inc_tax' => '25.0000',
        ];
    }
}
