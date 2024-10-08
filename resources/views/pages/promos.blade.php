@extends('layout')

@section('title', 'Promotions')

@section('content')
    <div id="loading" class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <div id="content">
        <h1 class="promo-title">Check out our promos!</h1>

        @if ($promos->isEmpty())
            <p class="no-promos">No promotions available.</p>
        @else
            <ul class="promo-list">
                @foreach ($promos as $index => $promo)
                    <li class="promo-item promo-slide {{ $index % 2 == 0 ? 'slide-left' : 'slide-right' }}">
                        <img src="data:image/jpeg;base64,{{ base64_encode($promo->image) }}" alt="Promo Image" class="promo-image">
                    </li>
                @endforeach
            </ul>
        @endif
        <script src="{{ asset('js/promos.js') }}"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/promos.css?v=2.5') }}">
@endsection
