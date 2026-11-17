<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function leaderboard(Request $request): View
    {
        $students = User::query()->where('role', 'student')->where('status', 'active');
        $search = trim((string) $request->query('q', ''));
        $filtered = clone $students;

        if ($search !== '') {
            $filtered->where('name', 'like', '%'.$search.'%');
        }

        $rankQuery = User::query()->from('users as ranked_users')->selectRaw('COUNT(*) + 1')
            ->where('ranked_users.role', 'student')->where('ranked_users.status', 'active')
            ->where(function ($before): void {
                $before->whereColumn('ranked_users.reputation', '>', 'users.reputation')
                    ->orWhere(function ($sameScore): void {
                        $sameScore->whereColumn('ranked_users.reputation', '=', 'users.reputation')
                            ->where(function ($tie): void {
                                $tie->whereColumn('ranked_users.name', '<', 'users.name')
                                    ->orWhere(function ($sameName): void {
                                        $sameName->whereColumn('ranked_users.name', '=', 'users.name')
                                            ->whereColumn('ranked_users.id', '<', 'users.id');
                                    });
                            });
                    });
            });
        $filtered->select('users.*')->addSelect(['leaderboard_rank' => $rankQuery]);

        $ranked = fn () => (clone $students)->orderByDesc('reputation')->orderBy('name')->orderBy('id');
        $podium = $ranked()->limit(3)->get();
        $leaders = $filtered->orderBy('leaderboard_rank')->paginate(20)->withQueryString();

        $myRank = null;
        $me = $request->user();
        if ($me?->isRole('student') && $me->status === 'active') {
            $ahead = (clone $students)->where('reputation', '>', $me->reputation)->count();
            $sameScoreAhead = (clone $students)->where('reputation', $me->reputation)->where(function ($query) use ($me): void {
                $query->where('name', '<', $me->name)
                    ->orWhere(fn ($tie) => $tie->where('name', $me->name)->where('id', '<', $me->id));
            })->count();
            $myRank = $ahead + $sameScoreAhead + 1;
        }

        return view('community.leaderboard', [
            'podium' => $podium,
            'leaders' => $leaders,
            'search' => $search,
            'myRank' => $myRank,
        ]);
    }

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

}
