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
        Schema::create('leads', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->string('company')->nullable();
        $table->decimal('budget', 10, 2); // Stores as 1500.00, 5000.00, etc.
        $table->text('message');
        $table->string('status')->default('new'); // For your own lead tracking
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
