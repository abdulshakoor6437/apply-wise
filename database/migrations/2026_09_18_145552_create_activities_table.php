<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('application_id');
            $table->foreign('application_id')->references('id')->on('applications')->cascadeOnDelete();
            $table->string('from_status')->nullable(); // null on first creation
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent(); // append-only, no updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
