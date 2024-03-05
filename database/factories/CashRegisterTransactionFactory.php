<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\CashRegisterTransaction;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\CashRegisterTransaction>
 */
class CashRegisterTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = CashRegisterTransaction::class;
    public function definition()
    {
        return [
            'cash_register_id' => 1,
            'amount' => '10000',
            'pay_method' => 'cash',
            'transaction_type' => 'sell',
            'transaction_id' => 1,
        ];
    }
}
