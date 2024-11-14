@extends('layout')

@section('title', 'Home | Ukay Supplier Consign')

@section('content')
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

    @if (!Auth::check()) 
        <div class="new-join-user-manual">
            <div class="youtube-video-container">
                <iframe src="https://www.youtube.com/embed/FT26GnIORbs" frameborder="0" allowfullscreen></iframe>
            </div>
            <hr>
            
            <div class="manual">
                @include('sections.faqSection') 
            </div>
            <hr> 
            <h2 class="responsive-heading">PAUNAWA!<br>
                Wala po kayong babayaran sa pag-checkout! 😊 Ang mga presyo na makikita (SRP) ay basehan nyo kung hanggang magkano niyo ito pwede ibenta. Ang consign price naman ay ang cost na babayaran niyo kapag nabenta nyo na ang isang partikular na item. 
                Nakalagay din kung magkano ang potensyal na kikitain nyo kung ibebenta niyo ito ayon sa SRP. 
                Lahat ng items ay galing sa US BALE!🛍️
            </h2>
            <hr>
        </div>
    @endif

    <div class="home-header">
        Order Now, Earn First, Pay Later
    </div>
    <hr/>

    {{--<div class="promo-modal" id="promoModal" style="display: none;">
        <div class="promo-modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2 class="welcome-message">
                Attention!!!

                Wala po kayong babayaran sa pag-checkout! 😊 Ang mga presyo na makikita ay basehan lang namin kapag nakabenta na kayo ng item. Nasa inyo po kung magkano niyo ito gustong ibenta. Lahat ng items ay galing sa US BALE!🛍️
            </h2>
        </div>
    </div>--}}

    <div class="home-page-cont">
        <div class="prodlist-cont">
            @include('sections.productList', ['products' => $groupedProducts]) 
        </div> 
        {{--<div class="cart-cont">
            @include('sections.cartList', ['carts' => $carts]) 
        </div>--}}
    </div>

    <script src="{{ asset('js/home.js?v=7.0') }}"></script>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/homePage.css?v=7.0') }}">
@endsection
