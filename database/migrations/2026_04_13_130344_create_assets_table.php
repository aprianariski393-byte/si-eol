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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 30)->unique();
            $table->string('name', 50);
            $table->string('category', 30)->nullable();

            $table->string('brand', 30)->nullable();
            $table->string('serial_number', 40)->unique()->nullable();

            $table->date('purchase_date')->nullable();
            $table->date('eol_date')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status', 20)->nullable();
            $table->string('department', 40)->nullable();

            $table->text('description')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
