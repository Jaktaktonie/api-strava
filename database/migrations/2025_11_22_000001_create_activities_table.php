<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['run', 'ride', 'walk']);
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedInteger('distance_meters')->default(0);
            $table->decimal('avg_speed_kmh', 5, 2)->nullable();
            $table->decimal('avg_pace', 5, 2)->nullable();
            $table->json('route')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('gpx_path')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'start_time']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
