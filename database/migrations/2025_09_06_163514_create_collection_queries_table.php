<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_queries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('CardCode');
            $table->string('CardName');
            $table->string('OverDueAmt');
            $table->string('Branch');
            $table->string('CollectorCode');
            $table->string('CollectorName');
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
        Schema::dropIfExists('collection_queries');
    }
}
