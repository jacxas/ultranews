@extends('layouts.app')

@section('title', 'Ingresar')

@section('content')
    <div class="auth-wrap">
        <div class="auth-card">
            <h1>Panel de administración</h1>
            <p class="auth-sub">Ingresá para gestionar las noticias de videos virales.</p>

            @if($errors->any())
                <div class="error-list">
                    <strong>No pudimos iniciar sesión:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-input" id="email" type="email" name="email"
                           value="{{ old('email') }}" required autofocus autocomplete="username">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Contraseña</label>
                    <input class="form-input" id="password" type="password" name="password"
                           required autocomplete="current-password">
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="remember" id="remember" value="1">
                    <label for="remember">Recordarme</label>
                </div>

                <button type="submit" class="btn" style="width: 100%">Ingresar</button>
            </form>

            @if(config('app.debug'))
                <p class="auth-hint">
                    Usuario demo (local): <code>admin@ultranews.test</code> / <code>password</code>
                </p>
            @endif
        </div>
    </div>
@endsection
