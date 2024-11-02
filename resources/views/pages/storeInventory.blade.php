@extends('layout')

@section('title', 'Stores Inventory')

@section('content')
    <div>
        <h1>Store Inventory List</h1>
        
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <link rel="stylesheet" href="{{ asset('css/storeInv.css?v=5.2') }}">
    </div>
@endsection
