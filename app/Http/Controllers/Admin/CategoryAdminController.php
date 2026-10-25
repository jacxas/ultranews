<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryAdminController extends Controller
{
    /**
     * Listado de categorías.
     */
    public function index(): View
    {
        $categories = Category::ordered()->withCount('articles')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Formulario de alta.
     */
    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category]);
    }

    /**
     * Guardar categoría nueva.
     */
    public function store(Request $request): RedirectResponse
    {
        $category = Category::create($this->validated($request));

        return redirect()
            ->route('admin.categories.index')
            ->with('status', "Categoría «{$category->name}» creada.");
    }

    /**
     * Formulario de edición.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    /**
     * Actualizar categoría.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));

        return redirect()
            ->route('admin.categories.index')
            ->with('status', "Categoría «{$category->name}» actualizada.");
    }

    /**
     * Eliminar categoría (sus noticias quedan sin categoría).
     */
    public function destroy(Category $category): RedirectResponse
    {
        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', "Categoría «{$name}» eliminada.");
    }

    /**
     * @return array<string, mixed>
     */
    protected function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'nullable', 'string', 'max:140',
                Rule::unique('categories', 'slug')->ignore($category?->id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]) + ['slug' => null, 'description' => null, 'color' => '#e11d48', 'sort_order' => 0];
    }
}
