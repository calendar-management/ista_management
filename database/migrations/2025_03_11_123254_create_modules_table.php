<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('modules', function (Blueprint $table) {
            $table->id('id_module');
            $table->string('code_module');
            $table->string('name');
            $table->float('hours');
            $table->float('mh_presentiel');
            $table->float('mh_distanciel');
            $table->string('regional');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('modules');
    }
};

