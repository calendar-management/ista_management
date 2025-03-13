<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('progress', function (Blueprint $table) {
            $table->id('id_progress');
            $table->foreignId('id_teaching')->constrained('teaching')->onDelete('cascade');
            $table->integer('hours_completed');
            $table->integer('remaining_hours');
            $table->json('hours_affected');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('progress');
    }
};
