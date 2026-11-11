<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\PlatformSettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');
Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
Route::get('/questions/similar', [QuestionController::class, 'similar'])->middleware(['auth', 'throttle:30,1'])->name('questions.similar');
Route::get('/questions/{question}', [QuestionController::class, 'show'])->name('questions.show');
Route::post('/language', function (Request $request) {
    $data = $request->validate(['locale' => ['required', 'in:tj,ru,en']]);
    $request->session()->put('locale', $data['locale']);
    if ($request->user()) {
        $request->user()->update(['locale' => $data['locale']]);
    }

    return back();
})->name('language.update');

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:6,1');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/student/dashboard', StudentDashboardController::class)->middleware('role:student')->name('student.dashboard');
    Route::get('/student/questions', [StudentDashboardController::class, 'questions'])->middleware('role:student')->name('student.questions');
    Route::get('/student/answers', [StudentDashboardController::class, 'answers'])->middleware('role:student')->name('student.answers');

    Route::get('/questions/create', [QuestionController::class, 'create'])->middleware('role:student')->name('questions.create');
    Route::resource('questions', QuestionController::class)->except(['create', 'index', 'show']);
    Route::post('/questions/{question}/answers', [AnswerController::class, 'store'])->middleware('throttle:10,1')->name('answers.store');
    Route::put('/answers/{answer}', [AnswerController::class, 'update'])->name('answers.update');
    Route::delete('/answers/{answer}', [AnswerController::class, 'destroy'])->name('answers.destroy');
    Route::post('/answers/{answer}/accept', [AnswerController::class, 'accept'])->name('answers.accept');
    Route::post('/questions/{question}/vote', [QuestionController::class, 'vote'])->middleware('throttle:30,1')->name('questions.vote');
    Route::patch('/questions/{question}/close', [QuestionController::class, 'close'])->name('questions.close');
    Route::post('/answers/{answer}/vote', [AnswerController::class, 'vote'])->middleware('throttle:30,1')->name('answers.vote');
    Route::get('/attachments/{attachment}', [AttachmentController::class, 'question'])->name('attachments.question');
    Route::get('/answer-attachments/{attachment}', [AttachmentController::class, 'answer'])->name('attachments.answer');
    Route::post('/reports', [ReportController::class, 'store'])->middleware('throttle:5,1')->name('reports.store');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
});

Route::prefix('moderator')->name('moderator.')->middleware(['auth', 'role:moderator,admin'])->group(function (): void {
    Route::get('/', [ModerationController::class, 'dashboard'])->name('dashboard');
    Route::get('/queue', [ModerationController::class, 'queue'])->name('queue');
    Route::post('/questions/{question}/{decision}', [ModerationController::class, 'question'])->name('questions.review');
    Route::post('/reports/{report}/{decision}', [ModerationController::class, 'report'])->name('reports.review');
    Route::get('/history', [ModerationController::class, 'history'])->name('history');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function (): void {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports/{type}', [AdminController::class, 'export'])->name('export');
    Route::get('/settings', [PlatformSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [PlatformSettingsController::class, 'update'])->name('settings.update');
    Route::get('/content', [AdminContentController::class, 'index'])->name('content.index');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
});
