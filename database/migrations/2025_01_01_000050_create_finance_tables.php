<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('category', 48)->index();
            $table->string('description');
            $table->unsignedBigInteger('amount');
            $table->date('spent_on')->index();
            $table->string('receipt_path')->nullable();
            $table->timestamps();
        });

        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('category', 48);
            $table->char('period', 7); // YYYY-MM
            $table->unsignedBigInteger('amount');
            $table->timestamps();
            $table->unique(['category', 'period']);
        });

        // Jurnal arus kas: setiap uang masuk/keluar tercatat otomatis di sini.
        Schema::create('cash_mutations', function (Blueprint $table) {
            $table->id();
            $table->string('direction', 3)->index(); // in | out
            $table->unsignedBigInteger('amount');
            $table->string('method', 16)->default('cash');
            $table->string('description');
            $table->nullableMorphs('source'); // sale | purchase | expense
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('occurred_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_mutations');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('expenses');
    }
};
