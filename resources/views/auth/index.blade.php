@extends('layouts.auth.index')

@section('content')

<div class="p-lg-5 p-4">
    <div class="text-center">
        <h5 class="text-primary">Selamat Datang !</h5>
        <p class="text-muted">Silahkan login untuk masuk ke halaman admin.</p>
    </div>

    <div class="mt-4">
        <form method="post" action="{{ route('login.perform') }}">
        @csrf
            <div class="mt-3">
                <label for="username" class="form-label">Email</label>
                <input type="email" name="username" autofocus="true" autocomplete="off" class="form-control" id="username" placeholder="Enter Email ...">
                @if ($errors->has('username'))
                    <small class="text-danger"><i>{{ $errors->first('username') }}</i></small>
                @endif
            </div>
            <div class="mt-1">
                <label for="password" class="form-label">Password</label>
                <div class="position-relative auth-pass-inputgroup show_hide_password">
                    <input type="password" name="password" autocomplete="off" class="form-control pe-5 password-input" id="password" placeholder="Enter Password ...">
                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" type="button"><b><i class="bx bx-hide fs-20 align-middle"></i></b></button>
                </div>
                @if ($errors->has('password'))
                    <small class="text-danger"><i>{{ $errors->first('password') }}</i></small>
                @endif
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary w-100"><i class="bx bxs-lock-open"></i> Sign in</button>
            </div>
        </form>
    </div>
</div>

@endsection