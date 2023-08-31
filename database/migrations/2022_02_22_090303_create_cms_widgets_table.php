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
        Schema::create('cms_widgets', function (Blueprint $table) {
            $table->id();
            $table->string('title',200);
            $table->string('slug',200)->unique();
            $table->string('description')->nullable();
            $table->string('icon',200);
            $table->string('color',200);
            $table->string('div_column_class',200);
            $table->text('link');
            $table->enum('widget_type',['small_box','line_chart','bar_chat','pie_chart','map_chart']);
            $table->text('sql',200);
            $table->text('config')->nullable();
            $table->enum('status',['1','0'])->default('0');
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);

            $table->index('widget_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_widgets');
    }
};
