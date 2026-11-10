<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 20)->default('student')->index();
            $table->string('status', 20)->default('active')->index();
            $table->integer('reputation')->default(0);
            $table->string('locale', 5)->default('tj');
            $table->string('avatar_path')->nullable();
        });

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title', 180);
            $table->longText('body');
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedBigInteger('accepted_answer_id')->nullable();
            $table->timestamps();
            $table->index(['title', 'status']);
            $table->index(['category_id', 'status', 'created_at']);
        });
        if (DB::getDriverName() === 'mysql') {
            Schema::table('questions', function (Blueprint $table): void {
                $table->fullText(['title', 'body']);
            });
        }

        Schema::create('answers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->longText('body');
            $table->string('status', 20)->default('published')->index();
            $table->timestamps();
            $table->index(['question_id', 'created_at']);
        });

        Schema::table('questions', function (Blueprint $table): void {
            $table->foreign('accepted_answer_id')->references('id')->on('answers')->nullOnDelete();
        });

        Schema::create('votes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('votable');
            $table->smallInteger('value');
            $table->timestamps();
            $table->unique(['user_id', 'votable_type', 'votable_id']);
            $table->index(['votable_type', 'votable_id', 'value']);
        });

        Schema::create('reputation_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_key', 191)->unique();
            $table->string('reason', 40)->index();
            $table->integer('points');
            $table->nullableMorphs('source');
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('question_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('disk', 40)->default('private');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });

        Schema::create('answer_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('answer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('disk', 40)->default('private');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('reportable');
            $table->string('reason', 60);
            $table->text('details')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->unique(['user_id', 'reportable_type', 'reportable_id']);
        });

        Schema::create('moderation_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('moderator_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('moderatable');
            $table->string('action', 40);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['moderator_id', 'created_at']);
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->longText('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('platform_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('moderation_logs');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('answer_attachments');
        Schema::dropIfExists('question_attachments');
        Schema::dropIfExists('reputation_transactions');
        Schema::dropIfExists('votes');
        Schema::table('questions', function (Blueprint $table): void {
            $table->dropForeign(['accepted_answer_id']);
        });
        Schema::dropIfExists('answers');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('categories');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['role', 'status', 'reputation', 'locale', 'avatar_path']);
        });
    }
};
