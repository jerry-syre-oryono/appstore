<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('app_name');
            $table->string('package_name');
            $table->text('description')->nullable();
            $table->string('apk_url')->nullable();
            $table->string('temp_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reviewer_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('submissions'); }
};
