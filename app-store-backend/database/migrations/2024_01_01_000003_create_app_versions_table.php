<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained()->cascadeOnDelete();
            $table->integer('version_code');
            $table->string('version_name');
            $table->string('apk_url');
            $table->string('file_hash')->nullable();
            $table->text('changelog')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_force')->default(false);
            $table->bigInteger('file_size')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('app_versions'); }
};
