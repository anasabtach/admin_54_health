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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_group_id')->constrained('user_groups')->onDelete('cascade');
            $table->enum('user_type',['admin','user'])->default('user');
            $table->string('name',100);
            $table->string('business_name',100)->nullable();
            $table->integer('business_category_id')->default(0);
            $table->integer('promote_category_id')->default(0);
            $table->string('username',100)->unique();
            $table->string('slug',100)->unique();
            $table->string('work_email',100)->nullable();
            $table->string('email',100)->unique()->nullable();
            $table->string('mobile_no',50)->unique()->nullable();
            $table->string('password',255);
            $table->text('image_url')->nullable();
            $table->text('banner_image_url')->nullable();
            $table->text('id_card')->nullable();
            $table->string('blur_image',200)->nullable();
            $table->string('profession',100)->nullable();
            $table->string('country',100)->nullable();
            $table->string('city',100)->nullable();
            $table->string('state',100)->nullable();
            $table->string('zipcode',100)->nullable();
            $table->string('address',100)->nullable();
            $table->string('latitude',100)->nullable();
            $table->string('longitude',100)->nullable();
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->text('about',5000)->nullable();
            $table->text('product_service',1000)->nullable();
            $table->string('site_url',200)->nullable();
            $table->integer('total_rating')->default(0);
            $table->integer('total_review')->default(0);
            $table->string('invite_code');
            $table->date('subscription_expiry_date')->nullable();
            $table->enum('status',['1','0'])->default('1');
            $table->enum('is_email_verify',['1','0'])->default('0');
            $table->dateTime('email_verify_at')->nullable();
            $table->enum('is_mobile_verify',['1','0'])->default('0');
            $table->dateTime('mobile_verify_at')->nullable();
            $table->enum('account_approved',['1','0'])->default('0');
            $table->enum('online_status',['1','0'])->default('0');
            $table->enum('notification_setting',['1','0'])->default('1');
            $table->string('referral_id',100)->unique();
            $table->string('mobile_otp',100)->nullable();
            $table->string('email_otp',100)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);

            $table->index(['user_group_id','slug','email','mobile_no','is_email_verify','status'],'index1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');

    }
};
