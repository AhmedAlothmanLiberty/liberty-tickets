<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->string('title', 200);
            $table->text('description');

            $table->string('type', 20);   // bug | feature
            $table->string('status', 20); // submitted | verified | pending | assigned | resolved

            $table->unsignedTinyInteger('priority');      // 1..10
            $table->unsignedTinyInteger('created_level'); // 1..3 (level of creator)

            $table->unsignedBigInteger('created_by');     // user id (DebtPlete users)
            $table->unsignedBigInteger('assigned_to')->nullable(); // user id

            $table->boolean('escalation_requested')->default(false);

            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();

            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();

            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['created_by']);
            $table->index(['assigned_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
