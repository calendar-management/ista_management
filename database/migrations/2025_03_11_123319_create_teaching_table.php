<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('teaching', function (Blueprint $table) {
            $table->id('id_teaching');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_group')->constrained('groupes')->onDelete('cascade');
            $table->foreignId('id_module')->constrained('modules')->onDelete('cascade');
            $table->foreignId('id_fillier')->constrained('filliers')->onDelete('cascade');
            $table->string('creneau');  
            $table->string('type_seance');  
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('teaching');
    }
};
