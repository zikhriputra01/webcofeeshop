<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('trx_id', 20)->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('total_harga', 12, 2);
            $table->decimal('uang_bayar', 12, 2);
            $table->decimal('kembalian', 12, 2);
            $table->timestamp('tanggal')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
