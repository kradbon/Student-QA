@props(['user', 'size' => 'md'])
@php($sizes = ['sm' => 'size-8 text-xs', 'md' => 'size-11 text-sm', 'lg' => 'size-20 text-2xl'])
<span {{ $attributes->class(['inline-grid shrink-0 place-items-center overflow-hidden rounded-full bg-orange-100 font-bold text-orange-950', $sizes[$size] ?? $sizes['md']]) }}>
    @if($user->avatar_path)
        <img src="{{ asset('storage/'.$user->avatar_path) }}" alt="{{ $user->name }}" class="size-full object-cover">
    @else
        {{ Str::upper(Str::substr($user->name, 0, 1)) }}
    @endif
</span>
