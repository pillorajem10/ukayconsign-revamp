@extends('layout')

@section('title', 'Home | Ukay Supplier Consign')

@section('content')
<h1>Welcome to the Store</h1>
<div class="home-page-cont">
    <div>
        @include('sections.productList', ['products' => $products]) 
    </div> 
</div>
@endsection

@section('styles')

    <!-- public/css/homePage.css  -->
    <link rel="stylesheet" href="{{ asset('css/homePage.css') }}">
@endsection