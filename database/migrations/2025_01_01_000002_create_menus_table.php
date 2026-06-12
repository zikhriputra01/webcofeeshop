<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nama_menu', 100);
            $table->enum('kategori', ['coffee', 'noncoffee', 'refreshment', 'snack']);
            $table->decimal('harga', 10, 2);
            $table->integer('stok')->default(0);
            $table->string('icon', 10)->nullable()->comment('Emoji icon for visual representation');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
