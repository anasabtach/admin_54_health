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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('module',100);
            $table->integer('module_id');
            $table->string('filename',200);
            $table->string('original_name',200);
            $table->text('file_url',5000);
            $table->string('file_url_blur',255);
            $table->text('thumbnail_url',5000)->nullable();
            $table->string('mime_type',50);
            $table->string('file_type',50);
            $table->string('driver',50)->default('local');
            $table->enum('media_type',['public','private'])->default('public');
            $table->text('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['module','module_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
};
