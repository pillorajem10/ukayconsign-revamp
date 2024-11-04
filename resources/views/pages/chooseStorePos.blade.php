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

    <div class="store-selection">
        <h2>Choose Store For POS</h2>
        <ul class="store-list">
            @foreach($stores as $store)
                <li class="store-item">
                    <a href="/pos?store_id={{ $store->id }}" class="store-link">
                        {{ $store->store_name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/chooseStorePos.css?v=5.5') }}">
@endsection
