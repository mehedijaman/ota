<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('type', 30)->default('promotional'); // promotional|transactional|due_reminder
            $table->string('to_phone', 30);
            $table->text('message');

            $table->unsignedInteger('parts')->default(1);
            $table->unsignedInteger('cost_credits')->default(1);

            $table->string('status', 30)->default('queued'); // queued|sent|failed
            $table->string('provider')->nullable();
            $table->string('provider_message_id')->nullable();
            $table->json('provider_response')->nullable();
            $table->string('error_message')->nullable();

            $table->timestamp('queued_at')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
