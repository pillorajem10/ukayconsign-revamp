@extends('layout')

@section('title', 'Choose Store for POS')

@section('content')
    @if(session('error'))
        <div id="error-message" class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div id="success-message" class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div>
        <h2>Choose Store For POS</h2>
        <div class="store-buttons">
            @foreach($stores as $store)
                <a href="/pos?store_id={{ $store->id }}" class="store-button">
                    {{ $store->store_name }}
                </a>
            @endforeach
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/ordersPage.css?v=1.6') }}">
@endsection
