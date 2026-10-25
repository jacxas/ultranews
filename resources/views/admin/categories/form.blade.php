@extends('layouts.admin')

@section('title', $category->exists ? 'Editar categoría' : 'Nueva categoría')

@section('content')
    <div class="admin-page-head">
        <h1>{{ $category->exists ? 'Editar categoría' : 'Nueva categoría' }}</h1>
        <a class="btn btn-small btn-ghost" href="{{ route('admin.categories.index') }}">← Volver al listado</a>
    </div>

    @if($errors->any())
        <div class="error-list">
            <strong>Revisá los siguientes errores:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel" style="max-width: 640px">
        <div class="panel-body">
            <form method="POST"
                  action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
                @csrf
                @if($category->exists)
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label class="form-label" for="name">Nombre *</label>
                    <input class="form-input" id="name" name="name" type="text"
                           value="{{ old('name', $category->name) }}" required maxlength="120">
                </div>

                <div class="form-group">
                    <label class="form-label" for="slug">Slug</label>
                    <input class="form-input" id="slug" name="slug" type="text"
                           value="{{ old('slug', $category->slug) }}" placeholder="se-genera-del-nombre">
                    <p class="form-help">Opcional: si lo dejás vacío se genera automáticamente.</p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Descripción</label>
                    <textarea class="form-textarea" id="description" name="description"
                              maxlength="255">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="color">Color</label>
                        <input class="form-input" id="color" name="color" type="color"
                               value="{{ old('color', $category->color ?: '#e11d48') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="sort_order">Orden</label>
                        <input class="form-input" id="sort_order" name="sort_order" type="number" min="0"
                               value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">
                        {{ $category->exists ? 'Guardar cambios' : 'Crear categoría' }}
                    </button>
                    <a class="btn btn-ghost" href="{{ route('admin.categories.index') }}">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
