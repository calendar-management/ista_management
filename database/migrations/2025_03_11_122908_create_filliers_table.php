<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('filliers', function (Blueprint $table) {
            $table->id('id_fillier');
            $table->string('name')->unique();
            $table->string('code_fillier');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('filliers');
    }
};

