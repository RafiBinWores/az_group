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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('cutting_id')->constrained('cuttings')->onDelete('cascade');
            $table->foreignId('embroidery_id')->nullable()->constrained('embroideries')->onDelete('set null');
            $table->foreignId('print_id')->nullable()->constrained('print_reports')->onDelete('set null');
            $table->date('garment_date');
            $table->json('production_data');
            $table->date('date');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
