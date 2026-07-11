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
            $table->string('asset_code', 50)->unique();
            $table->string('name');
            $table->string('category')->nullable();

            $table->string('brand', 100)->nullable();
            $table->string('serial_number', 100)->unique()->nullable();

            $table->date('purchase_date')->nullable();
            $table->date('eol_date')->nullable();

            $table->string('status')->nullable();
            $table->string('department')->nullable();

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
