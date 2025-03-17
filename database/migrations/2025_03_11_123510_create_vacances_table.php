<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('vacances', function (Blueprint $table) {
            $table->id('id_vacance');
            $table->string('nom_vacance');
            $table->string('type');
            $table->string('groupe');
            $table->string('filliere');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('vacances');
    }
};
