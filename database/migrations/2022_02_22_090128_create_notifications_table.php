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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('identifier');
            $table->foreignId('actor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('target_id')->constrained('users')->onDelete('cascade');
            $table->string('module');
            $table->integer('module_id');
            $table->integer('reference_id')->nullable();
            $table->string('reference_module')->nullable();
            $table->string('title');
            $table->text('description');
            $table->text('web_redirect_link')->nullable();
            $table->enum('is_read',['0','1'])->default('0');
            $table->enum('is_view',['0','1'])->default('0');
            $table->timestamps();

            $table->index(['unique_id','identifier','target_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
