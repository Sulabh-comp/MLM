@extends('layouts.manager.master')

@section('title', 'Manager Login')

@section('content')
<!-- Login -->
<div class="card">
    <div class="card-body">
        <!-- Logo -->
        <div class="app-brand justify-content-center mb-4">
            <a href="#" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                    <img src="{{ asset('logo.png') }}" alt="{{env('SITE_NAME')}}" width="32" height="32">
                </span>
                <span class="app-brand-text demo text-body fw-bold">{{env('SITE_NAME')}}</span>
            </a>
        </div>
        <!-- /Logo -->
        
        <h4 class="mb-1 pt-2">Welcome Manager! 👋</h4>
        <p class="mb-4">Please sign-in to your account and manage your region</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form class="mb-3" method="POST" action="{{ route('manager.login.post') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" 
                       placeholder="Enter your email" autofocus required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">Password</label>
                </div>
                <div class="input-group input-group-merge">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" 
                           placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                           aria-describedby="password" required>
                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
            </div>
        </form>

        <p class="text-center">
            <span>Regional Management Portal</span>
        </p>
    </div>
</div>
<!-- /Login -->
