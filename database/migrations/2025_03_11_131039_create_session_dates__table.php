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
        Schema::create('custom_session_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_progress');
            $table->integer('week_index');
            $table->date('session_date');
            $table->timestamps();
            
            $table->foreign('id_progress')->references('id_progress')->on('progress')->onDelete('cascade');
            $table->unique(['id_progress', 'week_index']);
        });
        
        // Add start_date column to progress table if it doesn't exist
        Schema::table('progress', function (Blueprint $table) {
            if (!Schema::hasColumn('progress', 'start_date')) {
                $table->date('start_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_session_dates');
        
        // Only drop the column if we added it
        if (Schema::hasColumn('progress', 'start_date')) {
            Schema::table('progress', function (Blueprint $table) {
                $table->dropColumn('start_date');
            });
        }
    }
};