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
        Schema::create('cms_module_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_group_id')->constrained('user_groups')->onDelete('cascade');
            $table->foreignId('cms_module_id')->constrained('cms_modules')->onDelete('cascade');
            $table->enum('is_add',['1','0'])->default('0');
            $table->enum('is_view',['1','0'])->default('0');
            $table->enum('is_update',['1','0'])->default('0');
            $table->enum('is_delete',['1','0'])->default('0');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_module_permissions');

    }
};
