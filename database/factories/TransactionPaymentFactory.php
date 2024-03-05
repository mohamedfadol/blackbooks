<?php

namespace Database\Factories;
use App\TransactionPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\TransactionPayment>
 */
class TransactionPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = TransactionPayment::class;
    public function definition()
    {
        return [
            'transaction_id' => 1,
            'business_id' => 1,
            'amount' => '100',
            'method' => 'cash',
            'card_type' => 'credit',
            'paid_on' => now(),
            'created_by' => 1,
            'is_advance' => 0,
            'paid_through_link' => 0,
            'payment_ref_no' => 'SP2023/0001',
        ];
    }
}
