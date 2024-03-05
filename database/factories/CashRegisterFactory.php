<?php

namespace Database\Factories;
use App\CashRegister;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\CashRegister>
 */
class CashRegisterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = CashRegister::class;
    public function definition()
    {
        return [
            'business_id' => 1,
            'location_id' => 1,
            'user_id' => 1,
            'status' => 'open',
            'total_card_slips' => 0,
        ];
    }
}
