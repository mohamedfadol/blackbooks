<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('global_currency_name')->nullable();
            $table->decimal('global_currency_value', 18, 9)->default(6,4);
            $table->string('local_currency_name')->nullable();
            $table->decimal('local_currency_value', 18, 9)->default(6,4);
            $table->integer('created_by')->unsigned();
            $table->integer('business_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_currencies');
    }
};
