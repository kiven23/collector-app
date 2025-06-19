<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_id');
            $table->string('employee');
            $table->string('datehired');
            $table->string('position');
            $table->string('brand');
            $table->string('allied_sales_qouta');
            $table->string('sales_performance');
            $table->string('product_bonus_total');
            $table->string('performance_assessment');
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
        Schema::dropIfExists('sales_employees');
    }
}
