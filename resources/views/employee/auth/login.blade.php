@extends('layouts.employee.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <img src="{{ asset('logo.png') }}" alt="Login Banner" class="img-fluid" width="100%" style="object-fit: cover">
            </div>
            <div class="col-md-6 col-md-offset-3">
                <h2>Login As An Employee</h2>
                <form method="POST" action="{{ route('employee.login.post') }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="form-group
                        @if ($errors->has('email') || $errors->has('password'))
                            has-error
                        @endif
                    ">
                        @if ($errors->has('email'))
                            <span class="help-block">{{ $errors->first('email') }}</span>
                        @endif
                        @if ($errors->has('password'))
                            <span class="help-block">{{ $errors->first('password') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection