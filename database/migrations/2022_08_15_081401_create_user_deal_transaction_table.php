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
        Schema::create('user_deal_transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('deal_id')->constrained('deals')->onDelete('cascade');
            $table->text('gateway_transaction_id',1000)->default('0');
            $table->text('gateway_original_transaction_id')->default('0');
            $table->string('gateway',100)->nullable();
            $table->string('charge_amount',100)->default('0');
            $table->date('expiry_date');
            $table->enum('device_type',['web','ios','android']);
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_deal_transaction');
    }
};
