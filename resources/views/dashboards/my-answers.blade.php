@extends('layouts.app')
@section('title', __('messages.my_answers'))
@section('content')
<h1 class="text-3xl font-black">{{ __('messages.my_answers') }}</h1><div class="mt-5 divide-y divide-slate-100 rounded-2xl border border-slate-200 bg-white px-5">@forelse($answers as $answer)<a class="block py-4" href="{{ route('questions.show', $answer->question) }}#answer-{{ $answer->id }}"><p class="font-medium">{{ Str::limit($answer->body, 220) }}</p><p class="mt-1 text-sm text-orange-700">{{ $answer->question->title }}</p><p class="mt-1 text-xs text-slate-500">{{ $answer->created_at->diffForHumans() }}</p></a>@empty<p class="py-8 text-slate-500">{{ __('messages.no_my_answers') }}</p>@endforelse</div><div class="mt-5">{{ $answers->links() }}</div>
@endsection
