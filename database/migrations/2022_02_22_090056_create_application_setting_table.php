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
        Schema::create('application_setting', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->index('identifier');
            $table->string('meta_key')->index('meta_key');
            $table->string('value');
            $table->tinyInteger('is_file')->default(0);
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
        Schema::dropIfExists('application_setting');

    }
};
