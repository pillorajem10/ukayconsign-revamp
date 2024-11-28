@extends('layout')

@section('title', 'Login')

@section('content')
    <div class="logpage-loading-overlay" id="logPageloadingOverlay">
        <div class="logpage-spinner"></div>
    </div>

    <div class="container mt-5">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{--<div class="promo-modal" id="promoModal" style="display: none;">
            <div class="promo-modal-content">
                <span class="close" id="closeModal">&times;</span>
                <h2 class="welcome-message">Check out our promos for you!</h2>
                @if($promos->isEmpty())
                    <p>No promos available.</p>
                @else
                    <div class="promo-images">
                        @foreach($promos as $promo)
                            @php
                                $imageData = base64_encode($promo->image);
                                $src = 'data:image/jpeg;base64,' . $imageData;
                            @endphp
                            <img src="{{ $src }}" alt="Promo Image" class="promo-image">
                        @endforeach
                    </div>
                @endif
            </div>
        </div> --}}

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg rounded login-card">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Login</h3>
                        <form method="POST" action="{{ route('login') }}" onsubmit="showLoading()">
                            @csrf
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-gradient btn-block">Login</button>
                        </form>
                        {{--<div class="mt-3 text-center">
                            <a href="/register" class="text-decoration-none">Don't have an account? Register here</a>
                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('js/login.js?v=7.8') }}"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/loginPage.css?v=7.8') }}">
@endsection
