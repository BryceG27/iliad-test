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
        /* 
        * The type of order can be: 0 - Order from a Supplier means the order is to add product available quantities, 1 - Order from a Customer means the product available quantities will be reduced
        */

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->comment('The user who made the order');
            $table->string('name');
            $table->text('description');
            $table->date('date')->comment('The date the order was made');
            $table->boolean('type')->default(0)->comment('The type of order: 0 - Order from a Supplier, 1 - Order from a Customer'); 
            $table->unsignedTinyInteger('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
