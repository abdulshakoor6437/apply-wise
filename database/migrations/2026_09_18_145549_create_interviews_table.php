<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('application_id');
            $table->foreign('application_id')->references('id')->on('applications')->cascadeOnDelete();
            $table->string('round_type'); // phone_screen, technical, behavioral, onsite, final, other
            $table->dateTime('scheduled_at');
            $table->string('format')->nullable(); // remote, onsite, hybrid
            $table->text('prep_notes')->nullable();
            $table->string('outcome')->nullable(); // pending, passed, failed, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
