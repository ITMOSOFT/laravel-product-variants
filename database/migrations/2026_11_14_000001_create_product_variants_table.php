<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('product-variants.table_prefix', 'itmosoft_');

        Schema::create($prefix.'product_variants', function (Blueprint $table): void {
            $table->id();
            $table->morphs('product');
            $table->string('sku', 100)->nullable()->unique();
            $table->string('barcode', 100)->nullable()->index();
            $table->string('name');
            $table->json('options')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->decimal('weight', 10, 3)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['product_type', 'product_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('product-variants.table_prefix', 'itmosoft_').'product_variants');
    }
};
