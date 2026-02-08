<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with(['children', 'posts']);

        // Filter by parent categories only
        if ($request->filled('parent_only')) {
            $query->whereNull('parent_id');
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('guard_name', 'like', "%$search%");
            });
        }

        $perPage = (int) $request->input('per_page', 20);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 20;
        $categories = $query->orderBy('name')->paginate($perPage);

        // Get statistics
        $stats = [
            'total_categories' => Category::count(),
            'parent_categories' => Category::whereNull('parent_id')->count(),
            'sub_categories' => Category::whereNotNull('parent_id')->count(),
            'categories_with_posts' => Category::has('posts')->count(),
        ];

        return view('admin.categories.index', compact('categories', 'stats', 'perPage'));
    }

    public function show(Category $category)
    {
        $category->load(['children', 'posts.user', 'posts.category']);

        // Get posts for this category
        $perPage = (int) request()->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $posts = $category->posts()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Get subcategories with post counts
        $subcategories = $category->children()->withCount('posts')->get();

        return view('admin.categories.show', compact('category', 'posts', 'subcategories', 'perPage'));
    }

    public function create()
    {
        if (!request()->user()->canManageCategoriesFull()) {
            abort(403, 'You do not have permission to create categories.');
        }
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        if (!request()->user()->canManageCategoriesFull()) {
            abort(403, 'You do not have permission to create categories.');
        }
        $request->validate([
            'name' => 'required|string|max:100',
            'guard_name' => 'required|string|max:50|unique:categories,guard_name',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        if (!request()->user()->canManageCategoriesFull()) {
            abort(403, 'You do not have permission to edit categories.');
        }
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        if (!request()->user()->canManageCategoriesFull()) {
            abort(403, 'You do not have permission to edit categories.');
        }
        $request->validate([
            'name' => 'required|string|max:100',
            'guard_name' => 'required|string|max:50|unique:categories,guard_name,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if (!request()->user()->canManageCategoriesFull()) {
            abort(403, 'You do not have permission to delete categories.');
        }
        // Check if category has posts
        if ($category->posts()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing posts.');
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return back()->with('error', 'Cannot delete category with subcategories.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
