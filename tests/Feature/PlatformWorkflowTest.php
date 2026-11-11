<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionAttachment;
use App\Models\Report;
use App\Models\User;
use App\Notifications\PlatformNotification;
use App\Services\SimilarQuestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlatformWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_login_and_logout(): void
    {
        $this->post(route('register'), [
            'name' => 'New Student',
            'email' => 'new@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['email' => 'new@example.test', 'role' => 'student']);
        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
        $this->post(route('login'), ['email' => 'new@example.test', 'password' => 'secure-password'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_role_access_is_separated(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student)->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('moderator.dashboard'))->assertForbidden();
        $this->get(route('student.dashboard'))->assertOk();
    }

    public function test_signed_in_home_routes_each_role_to_its_workspace(): void
    {
        foreach ([
            'student' => 'student.dashboard',
            'moderator' => 'moderator.dashboard',
            'admin' => 'admin.dashboard',
        ] as $role => $dashboard) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('home'))
                ->assertRedirect(route($dashboard));
        }
    }

    public function test_student_can_create_update_and_delete_owned_question(): void
    {
        $student = User::factory()->create();
        $category = Category::factory()->create();
        $this->actingAs($student);

        $this->post(route('questions.store'), [
            'title' => 'How can I sort a PHP array by value?',
            'body' => 'I need a practical explanation of sorting an associative array while keeping its keys.',
            'category_id' => $category->id,
        ])->assertRedirect();

        $question = Question::firstOrFail();
        $this->assertSame('pending', $question->status);
        $this->put(route('questions.update', $question), [
            'title' => 'How can I sort a PHP array by its values?',
            'body' => 'I need a practical explanation of sorting an associative array while preserving its keys.',
            'category_id' => $category->id,
        ])->assertRedirect();
        $this->delete(route('questions.destroy', $question))->assertRedirect();
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    }

    public function test_student_can_close_owned_question_which_remains_publicly_readable(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)->patch(route('questions.close', $question))->assertForbidden();
        $this->actingAs($owner)->patch(route('questions.close', $question))->assertRedirect();
        $this->assertSame('closed', $question->fresh()->status);
        $this->get(route('questions.show', $question))->assertOk();
        $this->actingAs($other)->post(route('answers.store', $question), ['body' => 'This answer should be rejected for a closed question.'])
            ->assertForbidden();
    }

    public function test_question_attachment_is_stored_privately_and_checked_before_download(): void
    {
        Storage::fake('local');
        $student = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($student)->post(route('questions.store'), [
            'title' => 'How can I attach a course document safely?',
            'body' => 'I need to share a PDF example with classmates while keeping it out of the public folder.',
            'category_id' => $category->id,
            'attachments' => [UploadedFile::fake()->create('lesson.pdf', 60, 'application/pdf')],
        ])->assertRedirect();

        $attachment = QuestionAttachment::firstOrFail();
        Storage::disk('local')->assertExists($attachment->path);
        $this->actingAs(User::factory()->create())->get(route('attachments.question', $attachment))->assertForbidden();
        $this->actingAs($student)->get(route('attachments.question', $attachment))->assertOk();
    }

    public function test_home_student_moderator_and_admin_screens_render(): void
    {
        $question = Question::factory()->create();
        $this->get(route('home'))->assertOk()->assertSee(__('messages.landing_title'));
        $this->get(route('questions.index'))->assertOk()->assertSee($question->title);
        $this->get(route('questions.show', $question))->assertOk()->assertSee($question->title);
        $this->actingAs(User::factory()->create(['role' => 'moderator']))->get(route('moderator.dashboard'))->assertOk();
        $this->get(route('moderator.queue'))->assertOk();
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.content.index'))->assertOk();
    }

    public function test_question_discovery_supports_unanswered_sorting(): void
    {
        $answered = Question::factory()->create();
        Answer::factory()->create(['question_id' => $answered->id]);
        $unanswered = Question::factory()->create(['title' => 'A distinct question with no answer yet']);

        $response = $this->get(route('questions.index', ['sort' => 'unanswered']))->assertOk();
        $response->assertSee($unanswered->title)->assertDontSee($answered->title);
    }

    public function test_question_author_cannot_answer_their_own_question(): void
    {
        $author = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $author->id]);

        $this->actingAs($author)->post(route('answers.store', $question), [
            'body' => 'This self-answer must be rejected by the server side rules.',
        ])->assertForbidden();
        $this->assertDatabaseCount('answers', 0);
    }

    public function test_only_question_author_can_accept_and_award_goes_to_answer_author(): void
    {
        $owner = User::factory()->create();
        $answerer = User::factory()->create();
        $other = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $owner->id]);
        $answer = Answer::factory()->create(['question_id' => $question->id, 'user_id' => $answerer->id]);

        $this->actingAs($other)->post(route('answers.accept', $answer))->assertForbidden();
        $this->actingAs($owner)->post(route('answers.accept', $answer))->assertRedirect();
        $this->assertSame(15, $answerer->fresh()->reputation);
        $this->assertSame(0, $owner->fresh()->reputation);
        $this->actingAs($owner)->post(route('answers.accept', $answer))->assertRedirect();
        $this->assertDatabaseCount('reputation_transactions', 1);
        $this->assertSame(15, $answerer->fresh()->reputation);
    }

    public function test_user_can_mark_all_notifications_read(): void
    {
        $user = User::factory()->create();
        $user->notify(new PlatformNotification('Test notice', route('home'), 'test'));

        $this->actingAs($user)->post(route('notifications.read-all'))->assertRedirect();
        $this->assertDatabaseMissing('notifications', ['notifiable_id' => $user->id, 'read_at' => null]);
    }

    public function test_student_can_submit_and_edit_their_answer(): void
    {
        $author = User::factory()->create();
        $answerer = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $author->id]);

        $this->actingAs($answerer)->post(route('answers.store', $question), ['body' => 'Use asort to retain array keys while sorting values.'])
            ->assertRedirect();
        $answer = Answer::firstOrFail();
        $this->put(route('answers.update', $answer), ['body' => 'Use asort to retain the associative keys while sorting values.'])
            ->assertRedirect();
        $this->assertDatabaseHas('answers', ['id' => $answer->id, 'body' => 'Use asort to retain the associative keys while sorting values.']);
        $this->delete(route('answers.destroy', $answer))->assertRedirect();
        $this->assertDatabaseMissing('answers', ['id' => $answer->id]);
    }

    public function test_vote_updates_reputation_once_and_rejects_self_vote(): void
    {
        $author = User::factory()->create();
        $voter = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $author->id]);
        $answer = Answer::factory()->create(['question_id' => $question->id, 'user_id' => $author->id]);

        $this->actingAs($author)->post(route('answers.vote', $answer), ['value' => 1])->assertForbidden();
        $this->actingAs($voter)->post(route('answers.vote', $answer), ['value' => 1])->assertRedirect();
        $this->assertSame(10, $author->fresh()->reputation);
        $this->assertDatabaseCount('reputation_transactions', 1);
        $this->post(route('answers.vote', $answer), ['value' => 1])->assertRedirect();
        $this->assertSame(0, $author->fresh()->reputation);
        $this->assertDatabaseCount('votes', 0);
    }

    public function test_question_author_can_accept_one_answer_and_award_reputation(): void
    {
        $author = User::factory()->create();
        $answerer = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $author->id]);
        $answer = Answer::factory()->create(['question_id' => $question->id, 'user_id' => $answerer->id]);

        $this->actingAs($author)->post(route('answers.accept', $answer))->assertRedirect();
        $this->assertSame($answer->id, $question->fresh()->accepted_answer_id);
        $this->assertSame(15, $answerer->fresh()->reputation);
        $this->assertDatabaseHas('reputation_transactions', ['event_key' => 'accepted:answer:'.$answer->id, 'points' => 15]);
    }

    public function test_moderator_can_approve_question_and_create_history(): void
    {
        $moderator = User::factory()->create(['role' => 'moderator']);
        $question = Question::factory()->create(['status' => 'pending']);

        $this->actingAs($moderator)->post(route('moderator.questions.review', [$question, 'approve']))->assertRedirect();
        $this->assertDatabaseHas('questions', ['id' => $question->id, 'status' => 'published']);
        $this->assertDatabaseHas('moderation_logs', ['moderator_id' => $moderator->id, 'action' => 'question_approve']);
        $this->assertDatabaseCount('notifications', 1);

        $rejected = Question::factory()->create(['status' => 'pending']);
        $this->post(route('moderator.questions.review', [$rejected, 'reject']))->assertRedirect();
        $this->assertDatabaseHas('questions', ['id' => $rejected->id, 'status' => 'rejected']);
        $this->assertDatabaseHas('moderation_logs', ['moderator_id' => $moderator->id, 'action' => 'question_reject']);
    }

    public function test_moderator_can_hide_reported_answer_and_remove_its_vote_reputation(): void
    {
        $author = User::factory()->create();
        $answerer = User::factory()->create();
        $voter = User::factory()->create();
        $moderator = User::factory()->create(['role' => 'moderator']);
        $question = Question::factory()->create(['user_id' => $author->id]);
        $answer = Answer::factory()->create(['question_id' => $question->id, 'user_id' => $answerer->id]);

        $this->actingAs($voter)->post(route('answers.vote', $answer), ['value' => 1])->assertRedirect();
        $this->post(route('reports.store'), ['type' => 'answer', 'id' => $answer->id, 'reason' => 'spam'])->assertRedirect();
        $report = Report::firstOrFail();
        $this->actingAs($moderator)->post(route('moderator.reports.review', [$report, 'hide']))->assertRedirect();

        $this->assertDatabaseHas('answers', ['id' => $answer->id, 'status' => 'hidden']);
        $this->assertSame(0, $answerer->fresh()->reputation);
        $this->assertDatabaseCount('votes', 0);
    }

    public function test_admin_can_change_platform_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'default_locale' => 'ru',
            'registration_open' => '0',
            'upload_max_mb' => 8,
        ])->assertRedirect();

        $this->assertDatabaseHas('platform_settings', ['key' => 'default_locale', 'value' => 'ru']);
        $this->assertDatabaseHas('platform_settings', ['key' => 'registration_open', 'value' => '0']);
        $this->assertDatabaseHas('platform_settings', ['key' => 'upload_max_mb', 'value' => '8']);
    }

    public function test_similarity_service_returns_relevant_published_question(): void
    {
        $question = Question::factory()->create([
            'title' => 'How does PHP sort an associative array by value?',
            'body' => 'Use asort when you need values in order and want to keep their original keys.',
        ]);
        Question::factory()->create(['status' => 'pending', 'title' => $question->title]);

        $matches = app(SimilarQuestionService::class)->find('How to sort a PHP associative array by value and preserve keys?');
        $this->assertNotEmpty($matches);
        $this->assertSame($question->id, $matches[0]['id']);
    }

    public function test_csv_exports_require_admin_role(): void
    {
        $this->get(route('admin.export', 'users'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get(route('admin.export', 'users'))->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.export', 'users'))->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
