<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', ['categories' => Category::withCount('questions')->orderBy('name')->paginate(30)]);
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Category::create([...$data, 'slug' => $this->uniqueSlug($data['name'])]);

        return redirect()->route('admin.categories.index')->with('success', __('messages.category_saved'));
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', ['category' => $category]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request);
        $category->update([...$data, 'slug' => $this->uniqueSlug($data['name'], $category->id)]);

        return redirect()->route('admin.categories.index')->with('success', __('messages.category_saved'));
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->questions()->exists()) {
            return back()->with('error', __('messages.category_in_use'));
        }
        $category->delete();

        return back()->with('success', __('messages.category_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $number = 2;
        while (Category::where('slug', $slug)->when($exceptId, fn ($query) => $query->where('id', '<>', $exceptId))->exists()) {
            $slug = $base.'-'.$number++;
        }

        return $slug;
    }
}
