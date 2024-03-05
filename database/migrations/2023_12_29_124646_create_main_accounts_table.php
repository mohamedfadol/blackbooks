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
        Schema::create('main_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->integer('account_number')->nullable();
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');

            $table->unsignedBigInteger('account_category_id');
            $table->foreign('account_category_id')->references('id')->on('account_categories')->onDelete('cascade');

            $table->unsignedBigInteger('financial_statement_id');
            $table->foreign('financial_statement_id')->references('id')->on('financial_statements')->onDelete('cascade');


            $table->integer('parent_id')->nullable();
            $table->integer('is_closed')->default(0);
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->boolean('show_balance')->default(1);
            $table->decimal('totalBalance',)->default(6,4);
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('main_accounts');
    }
};
