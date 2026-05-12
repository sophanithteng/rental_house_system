<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Categories/Index', [
            'categories' => Category::orderByDesc('created_at')->get(),
            'isAdmin' => $request->user()?->isAdmin() ?? false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cat_name' => ['required', 'string', 'max:255', Rule::unique('categories', 'cat_name')],
            'base_price' => ['required', 'numeric', 'gt:0'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('images/categories', 'public');
        }
        Category::create($validated);

        return back()->with('success', 'បានបន្ថែមប្រភេទបន្ទប់ដោយជោគជ័យ។');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'cat_name' => ['required', 'string', 'max:255', Rule::unique('categories', 'cat_name')->ignore($category->id)],
            'base_price' => ['required', 'numeric', 'gt:0'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);
        if ($request->hasFile('photo')) {
            if (! empty($category->photo)) {
                Storage::disk('public')->delete($category->photo);
            }
            $validated['photo'] = $request->file('photo')->store('images/categories', 'public');
        }
        $category->update($validated);

        return back()->with('success', 'បានកែប្រែប្រភេទបន្ទប់ដោយជោគជ័យ។');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->rooms()->exists()) {
            return back()->with('error', 'មិនអាចលុបប្រភេទនេះបានទេ ព្រោះមានបន្ទប់កំពុងប្រើប្រាស់។');
        }
        if (! empty($category->photo)) {
            Storage::disk('public')->delete($category->photo);
        }
        $category->delete();

        return back()->with('success', 'បានលុបប្រភេទបន្ទប់ដោយជោគជ័យ។');
    }
}
