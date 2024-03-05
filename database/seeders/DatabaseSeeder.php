<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\TransactionSellingSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call([BarcodesTableSeeder::class,
        //     PermissionsTableSeeder::class,
        //     CurrenciesTableSeeder::class,
        // ]);

        $this->call([TransactionSeeder::class,
        TransactionPaymentSeeder::class,
        TransactionSellLineSeeder::class,
        CashRegistersSeeder::class,
        CashRegisterTransactionSeeder::class,
        ]);
    }
}
