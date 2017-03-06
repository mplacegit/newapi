<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('mp_widget_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',36)->unique();
            $table->string('title');
            $table->string('sizes');
            $table->string('description');
            $table->string('wid',36);
            $table->integer('template_type_id');
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
        //
        Schema::drop('mp_widget_templates');
    }
}
