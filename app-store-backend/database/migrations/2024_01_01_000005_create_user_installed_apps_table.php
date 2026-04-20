<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('user_installed_apps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('app_id')->constrained()->cascadeOnDelete();
            $table->integer('current_version_code');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('user_installed_apps'); }
};
