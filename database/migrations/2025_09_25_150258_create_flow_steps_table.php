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
        Schema::create('flow_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('flow_id')->constrained('flows')->cascadeOnDelete();
            $table->foreignUuid('next_step_id')->nullable()->constrained('flow_steps')->nullOnDelete();
            $table->string('expected_answer_type')->default('text')->comment("From FlowStepExpectedAnswerTypeEnum");
            $table->text('question_text');
            $table->boolean('is_start')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flow_steps');
    }
};
