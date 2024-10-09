@extends('layout')

@section('title', 'Home | Ukay Supplier Consign')

@section('content')
    <div class="promo-modal" id="promoModal" style="display: none;">
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
    </div> 

    <h1 class="welcome-header">Welcome{{ Auth::check() ? ', ' . Auth::user()->email : '' }}</h1>

    @if(session('success'))
        <div id="success-message" class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="error-message" class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    

    <div class="home-page-cont">
        <div class="prodlist-cont">
            @include('sections.productList', ['products' => $groupedProducts]) 
        </div> 
        <div class="cart-cont">
            @include('sections.cartList', ['carts' => $carts]) 
        </div>
    </div>

    <script src="{{ asset('js/home.js?v=2.9') }}"></script>
@endsection


@section('styles')
    <link rel="stylesheet" href="{{ asset('css/homePage.css?v=2.9') }}">
@endsection
