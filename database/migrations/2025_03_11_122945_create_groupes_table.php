<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('groupes', function (Blueprint $table) {
            $table->id('id_group');
            $table->string('name');
            $table->foreignId('id_fillier')->constrained('filliers')->onDelete('cascade');
            $table->string('niveau');
            $table->integer('effectif');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('groupes');
    }
};
