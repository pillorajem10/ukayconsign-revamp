@extends('layout')

@section('title', 'Register')

@section('content')
    <div class="regpage-loading-overlay" id="regPageloadingOverlay">
        <div class="regpage-spinner"></div>
    </div>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Register</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
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

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg rounded login-card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}" onsubmit="showLoading()">
                            @csrf
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-gradient btn-block">Register</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="/login" class="text-decoration-none">Already have an account? Login here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('js/registration.js?v=6.3') }}"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/loginPage.css?v=6.3') }}">
@endsection

