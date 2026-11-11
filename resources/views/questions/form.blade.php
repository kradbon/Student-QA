@extends('layouts.app')
@section('title', $question->exists ? __('messages.edit_question') : __('messages.ask_question'))
@section('content')
<div class="mx-auto max-w-3xl">
    <h1 class="text-3xl font-black">{{ $question->exists ? __('messages.edit_question') : __('messages.ask_question') }}</h1>
    <p class="mt-2 text-slate-600">{{ __('messages.question_help') }}</p>
    <form method="post" enctype="multipart/form-data" action="{{ $question->exists ? route('questions.update', $question) : route('questions.store') }}" class="mt-6 space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf @if($question->exists) @method('PUT') @endif
        <label class="block text-sm font-semibold text-slate-800">{{ __('messages.question_title') }}<input data-similar-title class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5" name="title" value="{{ old('title', $question->title) }}" minlength="12" maxlength="180" required></label>
        <label class="block text-sm font-semibold text-slate-800">{{ __('messages.details') }}<textarea data-similar-body class="mt-1.5 w-full rounded-lg border px-3.5 py-2.5 leading-6" name="body" rows="8" minlength="20" maxlength="20000" required>{{ old('body', $question->body) }}</textarea></label>
        <div data-similar-results data-endpoint="{{ route('questions.similar') }}{{ $question->exists ? '?except='.$question->id : '' }}" data-label="{{ __('messages.possible_duplicates') }}" data-loading="{{ __('messages.searching') }}" aria-live="polite" class="space-y-2"></div>
        <label class="block text-sm font-semibold text-slate-800">{{ __('messages.category') }}<select name="category_id" required class="mt-1.5 min-h-11 w-full rounded-lg border px-3.5 py-2.5"><option value="">{{ __('messages.choose_category') }}</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', $question->category_id) === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></label>
        <label class="block text-sm font-semibold">{{ __('messages.attachments') }}<input class="mt-1 block w-full rounded-xl border border-slate-300 p-2 text-sm" type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.txt,.doc,.docx,.zip"><span class="mt-1 block text-xs font-normal text-slate-500">{{ __('messages.attachment_help', ['max' => $uploadMaxMb]) }}</span></label>
        <div class="flex items-center justify-between"><a class="text-sm font-semibold text-slate-600" href="{{ $question->exists ? route('questions.show', $question) : route('questions.index') }}">{{ __('messages.cancel') }}</a><button class="btn-primary" type="submit">{{ $question->exists ? __('messages.save_changes') : __('messages.submit_question') }}</button></div>
    </form>
    @if($question->exists && $question->attachments->isNotEmpty())<div class="mt-5 rounded-2xl border border-slate-200 bg-white p-5"><h2 class="font-bold">{{ __('messages.attachments') }}</h2>@foreach($question->attachments as $file)<a class="mt-2 block text-sm text-orange-700" href="{{ route('attachments.question', $file) }}">{{ $file->original_name }}</a>@endforeach</div>@endif
</div>
@endsection
