@extends('layouts.app')
@section('title', 'Register')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        {{-- Normalized Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-slate-900 text-white rounded-lg mb-4 text-xl font-bold">S</div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Create Account</h1>
            <p class="text-slate-500 mt-1 text-sm">Join SmartShop and start saving</p>
        </div>

        {{-- Errors --}}
        @if($errors->any())
            <div class="alert-error mb-4">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Register Form --}}
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name" class="label">Full Name</label>
                <input type="text" id="name" name="name"
                       value="{{ old('name') }}"
                       class="input"
                       placeholder="John Doe"
                       required autofocus>
            </div>

            <div class="form-group">
                <label for="email" class="label">Email Address</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       class="input"
                       placeholder="you@example.com"
                       autocomplete="email"
                       required>
            </div>

            <div class="form-group">
                <label for="password" class="label">Password</label>
                <input type="password" id="password" name="password"
                       class="input"
                       placeholder="Min 8 characters"
                       autocomplete="new-password"
                       required minlength="8">
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="label">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="input"
                       placeholder="Repeat your password"
                       autocomplete="new-password"
                       required>
            </div>

            <button type="submit" class="btn-primary w-full shadow-md font-bold tracking-wider mt-4">
                CREATE ACCOUNT
            </button>
        </form>

        <div class="text-center mt-10">
            <p class="text-sm text-slate-500">
                Already have an account?
                <a href="{{ route('login') }}" class="text-indigo-600 font-bold hover:text-indigo-700 hover:underline decoration-2 underline-offset-4 transition-all ml-1">Sign in</a>
            </p>
        </div>

    </div>
</div>
@endsection
