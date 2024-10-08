@extends('layout')

@section('title', 'Home | Ukay Supplier Consign')

@section('content')
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

    <script src="{{ asset('js/home.js?v=2.6') }}"></script>
@endsection


@section('styles')
    <link rel="stylesheet" href="{{ asset('css/homePage.css?v=2.6') }}">
@endsection
