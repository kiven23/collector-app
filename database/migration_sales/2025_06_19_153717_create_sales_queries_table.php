<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_queries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('DocNum');              // SAP Document Number
            $table->string('NumAtCard');           // Customer Reference Number
            $table->string('DocDate');              // Date of Transaction
            $table->string('Day');                // Day (optional if parsing from date)
            $table->string('Year');               // Year
            $table->string('Month');              // Month
            $table->string('Branch');             // Store/Branch
            $table->string('Brand');              // Product Brand
            $table->string('Supplier');           // Supplier Name
            $table->string('Amt');        // Amount
            $table->string('Salesman');           // SAP Salesman Code
            $table->string('PromoName');          // Full Name of Promo/Agent
            $table->string('DateHired');           // Hiring Date
            $table->string('SlpName');            // Salesperson SAP Name
            $table->string('ItemCode');           // Product Code
            $table->string('ItemName');           // Product Name
            $table->string('Quota');      // Sales Quota
            $table->string('BQuota');     // Base Quota or Bonus Quota
            $table->string('Position');           // Job Position
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
        Schema::dropIfExists('sales_queries');
    }
}
