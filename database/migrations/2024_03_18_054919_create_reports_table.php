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
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('post_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('reporting_user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // The type of report from the ReportType enum
            $table->string('description', 255)->nullable(); // Additional details from the user if 'Other' is selected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
