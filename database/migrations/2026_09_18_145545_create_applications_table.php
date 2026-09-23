<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->string('job_title');
            $table->string('status')->default('wishlist');
            $table->date('date_applied')->nullable();
            $table->string('source')->nullable(); // linkedin, indeed, company_site, referral, other
            $table->text('job_posting_url')->nullable();
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->tinyInteger('excitement_rating')->nullable(); // 1-5
            $table->integer('priority')->default(0); // ordering within kanban column
            $table->date('next_action_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Fast Kanban queries: filter by user + status
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
