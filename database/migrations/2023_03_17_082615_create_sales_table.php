<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string("region");
            $table->string("country");
            $table->string("item_type");
            $table->tinyInteger("sales_channel")
                ->defaultValue(1)
                ->comment('0: Offline, 1: Online');
            $table->string("order_priority", 3);
            $table->date("order_date");
            $table->bigInteger("order_id");
            $table->date("ship_date");
            $table->integer("units_sold");
            $table->decimal("unit_price", 5, 2);
            $table->decimal("unit_cost", 5, 2);
            $table->decimal("total_revenue", 10, 2);
            $table->decimal("total_cost", 10, 2);
            $table->decimal("total_profit", 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
