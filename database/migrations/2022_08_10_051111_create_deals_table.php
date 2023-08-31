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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name',150);
            $table->string('slug',150)->unique();
            $table->text('image_url',1000);
            $table->string('blur_image')->nullable();
            $table->text('description',1000)->nullable();
            $table->enum('price_type',['special_price','off','free'])->nullable();
            $table->string('price',50)->nullable();
            $table->string('sale_price',50)->nullable();
            $table->string('discount_percentage',50)->nullable();
            $table->enum('time_bound',['ongoing','time_bound'])->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('redeem_type',['one_time','multiple'])->nullable();
            $table->integer('redeem_length')->nullable();
            $table->string('deal_code',50);
            $table->enum('status',['1','0'])->default();
            $table->enum('paid_promotion',['1','0'])->default('0');
            $table->date('paid_pormotion_expire_date')->nullable();
            $table->integer('total_rating')->default(0);
            $table->integer('total_review')->default(0);
            $table->integer('deal_redeemed_count')->default(0);
            $table->integer('deal_redeemed_user_count')->default(0);
            $table->integer('marketing_deal_redeemed_count')->default(0);
            $table->integer('marketing_deal_redeemed_user_count')->default(0);
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);

            $table->index(['paid_promotion'],'index1');
            $table->index(['deal_code'],'index2');
            $table->index(['status'],'index3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deals');
    }
};
