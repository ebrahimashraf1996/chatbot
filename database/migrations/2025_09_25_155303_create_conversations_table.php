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
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_phone');
            $table->foreignUuid('service_number_id')->constrained('service_numbers')->cascadeOnDelete();
            $table->foreignUuid('current_step_id')->nullable()->constrained('flow_steps')->nullOnDelete();
            $table->string('status')->default('active')->comment("From ConversationStatusEnum");
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
