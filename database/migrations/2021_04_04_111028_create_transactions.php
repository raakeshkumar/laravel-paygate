<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('paygate_id', 100)->nullable();
            $table->string('pay_request_id', 100)->nullable();
            $table->string('reference', 100)->nullable();
            $table->string('transaction_status', 100)->nullable();
            $table->string('result_code', 100)->nullable();
            $table->string('auth_code', 100)->nullable();
            $table->string('currency', 100)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('result_desc', 100)->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->string('risk_indicator', 100)->nullable();
            $table->string('pay_method', 100)->nullable();
            $table->string('pay_method_detail', 100)->nullable();
            $table->string('vault_id', 100)->nullable();
            $table->string('payvault_data_1', 100)->nullable();
            $table->string('payvault_data_2', 100)->nullable();
            $table->string('checksum', 100)->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
