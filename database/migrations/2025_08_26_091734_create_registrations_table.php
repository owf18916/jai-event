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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('gate_number')->nullable();
            $table->unsignedSmallInteger('ticket_count')->default(1);
            $table->boolean('is_using_bus')->default(false);
            $table->timestamp('scanned_at')->nullable();
            $table->foreignId('scanned_by')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'event_id'], 'unique_employee_event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
