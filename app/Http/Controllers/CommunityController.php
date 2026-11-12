<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function tags(Request $request): View
    {
        $query = Category::query()->where('is_active', true)
            ->withCount(['questions' => fn ($builder) => $builder->whereIn('status', ['published', 'closed'])]);

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        return view('community.tags', ['categories' => $query->orderBy('name')->paginate(24)->withQueryString()]);
    }

    public function users(Request $request): View
    {
        $query = User::query()->where('status', 'active')
            ->withCount(['questions' => fn ($builder) => $builder->whereIn('status', ['published', 'closed'])])
            ->withCount(['answers' => fn ($builder) => $builder->where('status', 'published')]);

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        return view('community.users', ['users' => $query->orderByDesc('reputation')->orderBy('name')->paginate(24)->withQueryString()]);
    }

    public function badges(): View
    {
        return view('community.badges', [
            'contributors' => User::query()->where('status', 'active')->orderByDesc('reputation')->orderBy('name')->take(12)->get(),
        ]);
    }
}
