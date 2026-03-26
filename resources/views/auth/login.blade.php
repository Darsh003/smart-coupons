@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-slate-900 text-white rounded-lg mb-4 text-xl font-bold">S</div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Welcome Back</h1>
            <p class="text-slate-500 mt-1 text-sm">Please sign in to your account</p>
        </div>

        {{-- Test Credentials Hint --}}
        <div class="bg-indigo-50/50 border border-indigo-100 rounded-lg p-4 mb-8">
            <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full animate-pulse"></span>
                Demo Accounts
            </p>
            <div class="space-y-1 text-xs">
                <p class="text-slate-600 font-medium">Premium: <span class="font-mono text-slate-900">premium@test.com / password</span></p>
                <p class="text-slate-600 font-medium">Standard: <span class="font-mono text-slate-900">regular@test.com / password</span></p>
            </div>
        </div>

        {{-- Errors --}}
        @if($errors->any())
            <div class="alert-error mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="label">Email Address</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       class="input"
                       placeholder="you@example.com"
                       required>
            </div>

            <div class="form-group">
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="label !mb-0">Password</label>
                </div>
                <input type="password" id="password" name="password"
                       class="input"
                       placeholder="••••••••"
                       autocomplete="current-password"
                       required>
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center gap-2 text-sm text-slate-500 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-indigo-600">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn-primary w-full shadow-md font-bold tracking-wider">
                SIGN IN
            </button>
        </form>

        <div class="text-center mt-10">
            <p class="text-sm text-slate-500">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-indigo-600 font-bold hover:text-indigo-700 hover:underline decoration-2 underline-offset-4 transition-all ml-1">Create one</a>
            </p>
        </div>

    </div>
</div>
@endsection
