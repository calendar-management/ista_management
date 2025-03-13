<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('progress_weekly', function (Blueprint $table) {
            $table->id('id_weekly');
            $table->foreignId('id_progress')->constrained('progress')->onDelete('cascade');
            $table->integer('week');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('progress_weekly');
    }
};

