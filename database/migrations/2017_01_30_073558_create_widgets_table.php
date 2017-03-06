<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('mp_widgets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('affiliate_id',36);
            $table->string('wid',36);
            $table->string('begun_pad_id',36);
            $table->integer('server_id');
            $table->integer('template_id');
            $table->tinyInteger('driver');
            $table->integer('offers_count');
            $table->integer('offers_link_count');
            $table->tinyInteger('enabled');
            $table->tinyInteger('no_search');
            $table->tinyInteger('islink');
            $table->string('search_categories',5000);
            $table->string('default_categories',4096);
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
        Schema::drop('mp_widgets');
    }
}
