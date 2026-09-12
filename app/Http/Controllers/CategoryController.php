<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['menuItems', 'inventoryItems'])->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        Category::create($validated);

        return back()->with('success', "Category '{$validated['name']}' created successfully.");
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $category->update($validated);

        return back()->with('success', "Category '{$category->name}' updated successfully.");
    }

    public function destroy(Category $category)
    {
        if ($category->menuItems()->count() > 0 || $category->inventoryItems()->count() > 0) {
            return back()->with('error', "Cannot delete category '{$category->name}' because menu items or raw materials belong to it.");
        }

        $category->delete();
        return back()->with('success', "Category deleted successfully.");
    }
}
