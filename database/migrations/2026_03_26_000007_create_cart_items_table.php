<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            // Guest or logged-in user — one of these will always be set
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('session_id', 191)->nullable();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');

            // Price snapshot at add-to-cart time (handles weekend surge)
            $table->decimal('unit_price', 10, 2);
            $table->decimal('base_price', 10, 2);
            $table->boolean('is_weekend_price')->default(false);

            $table->timestamps();

            // Unique: one row per product per user/session
            $table->unique(['user_id', 'product_id']);
            $table->unique(['session_id', 'product_id']);

            $table->index('user_id');
            $table->index('session_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
