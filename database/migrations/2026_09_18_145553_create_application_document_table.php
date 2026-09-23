<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_document', function (Blueprint $table) {
            $table->id();
            $table->uuid('application_id');
            $table->foreign('application_id')->references('id')->on('applications')->cascadeOnDelete();
            $table->uuid('document_id');
            $table->foreign('document_id')->references('id')->on('documents')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['application_id', 'document_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_document');
    }
};
