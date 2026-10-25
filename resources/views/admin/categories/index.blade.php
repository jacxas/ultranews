@extends('layouts.admin')

@section('title', 'Categorías')

@section('content')
    <div class="admin-page-head">
        <h1>Categorías</h1>
        <a class="btn btn-small" href="{{ route('admin.categories.create') }}">+ Nueva categoría</a>
    </div>

    <div class="panel">
        <div class="table-scroll">
            <table class="table-admin">
                <thead>
                    <tr>
                        <th>Color</th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Descripción</th>
                        <th>Noticias</th>
                        <th>Orden</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <span style="display:inline-block;width:1.1rem;height:1.1rem;border-radius:50%;background:{{ $category->color }}"></span>
                            </td>
                            <td class="strong">{{ $category->name }}</td>
                            <td><a href="{{ route('categories.show', $category) }}" target="_blank">/{{ $category->slug }} ↗</a></td>
                            <td>{{ \Illuminate\Support\Str::limit($category->description ?? '', 60) }}</td>
                            <td>{{ $category->articles_count }}</td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                <div class="actions-row">
                                    <a class="btn-tiny" href="{{ route('admin.categories.edit', $category) }}">Editar</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                          onsubmit="return confirm('¿Eliminar la categoría «{{ $category->name }}»? Sus noticias quedarán sin categoría.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-tiny danger" type="submit">🗑</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">Todavía no hay categorías.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel-body pagination-wrap" style="margin: 0; justify-content: flex-end">
            {{ $categories->links() }}
        </div>
    </div>
@endsection
