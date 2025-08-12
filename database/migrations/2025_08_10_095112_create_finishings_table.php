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
        Schema::create('finishings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->integer('thread_cutting');
            $table->integer('qc_check')->nullable();
            $table->integer('button_rivet_attach')->nullable();
            $table->integer('iron')->nullable();
            $table->integer('hangtag')->nullable();
            $table->integer('poly')->nullable();
            $table->integer('carton')->nullable();
            $table->integer('today_finishing')->nullable();
            $table->integer('total_finishing')->nullable();
            $table->integer('plan_to_complete')->nullable();
            $table->integer('dpi_inline')->nullable();
            $table->integer('fri_final')->nullable();
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
        Schema::dropIfExists('finishings');
    }
};
