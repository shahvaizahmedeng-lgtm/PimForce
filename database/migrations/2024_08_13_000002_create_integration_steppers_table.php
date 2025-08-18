<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->integer('step');
            $table->json('data')->nullable();
            $table->json('store_data')->nullable();
            $table->json('api_data')->nullable();
            $table->json('fields_mapping_data')->nullable();
            $table->json('seo_data')->nullable();
            $table->json('specifications')->nullable(); // array of strings
            $table->json('unique_identifier')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
