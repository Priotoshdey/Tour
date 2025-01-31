@extends('layouts.auth')
@section('content')

    <div class="container vh-100 d-flex align-items-center justify-content-center">

        <div class="card m-3">

            <div class="card-body">
                <h3>Signin</h3>
                <form action="" method="post">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" value="{{ old('email') }}"  class="form-control @error('email') is-invalid @enderror" required id="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required id="">
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-danger" type="submit">Login</button>
                        <a class="btn btn-success" href="{{ route('user.signup') }}">Signup</a>
                      </div>
                </form>
            </div>
        </div>

    </div>

@endsection
