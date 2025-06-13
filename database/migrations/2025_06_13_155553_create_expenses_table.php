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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->foreignId('paid_by')->constrained('users')->onDelete('cascade');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->string('split_method')->default('equal');
            $table->timestamps();
        });

        Schema::create('expense_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('share', 10, 2);
            $table->decimal('paid_share', 10, 2)->default(0);
            $table->decimal('owed_share', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_user');
    }
};
