@extends('layout')

@section('title', 'Stores Inventory')

@section('content')
    <div>
        <h1>Store Inventory List</h1>
        
        <!-- Display Success Message -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Display Error Message -->
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Search Input -->
        <form method="GET" action="{{ request()->url() }}">
            <input type="text" name="search" placeholder="Search by Product ID" class="search-input" value="{{ request()->get('search') }}" />
            <input type="hidden" name="store_id" value="{{ request()->get('store_id') }}" />
            <button type="submit">Search</button>
            <a href="{{ request()->url() . '?store_id=' . request()->get('store_id') }}" class="clear-button">Clear</a>
        </form>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product ID</th>
                        <th>Stocks</th>
                        <th>Consign</th>
                        <th>SRP</th>
                        <th>Action</th> <!-- New Action column -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory as $item)
                    <tr>
                        <td>{{ $item->SKU }}</td>
                        <td>{{ $item->ProductID }}</td>
                        <td>{{ $item->Stocks }}</td>
                        <td>{{ $item->Consign }}</td>
                        <td>{{ $item->SPR }}</td>
                        <td>
                            <a href="{{ route('usc-returns.create', ['product_sku' => $item->SKU, 'store_id' => request()->get('store_id')]) }}" class="btn btn-primary">
                                Request Return
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <link rel="stylesheet" href="{{ asset('css/storeInv.css?v=5.5') }}">
    </div>
@endsection
