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
            $table->foreignUuid('flow_id')->constrained('flows')->onDelete('cascade');
            $table->integer('step_order');
            $table->text('question_text');
            $table->string('expected_answer_type')->default('any')->comment("From FlowStepExpectedAnswerTypeEnum");
            $table->json('options')->nullable();
            $table->foreignUuid('next_step_id')->nullable()->constrained('flow_steps')->nullOnDelete();
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
