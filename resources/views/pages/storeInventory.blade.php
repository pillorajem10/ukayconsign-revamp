@extends('layout')

@section('title', 'Stores Inventory')

@section('content')
    <div>
        <div class="back-to-dashboard">
            <a href="{{ route('stores.index') }}" class="btn btn-secondary">Back to Stores</a>
        </div>

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
                        <th>Retail Price</th>
                        <th>Action</th> <!-- Action column for all buttons -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory as $item)
                    <tr>
                        <td>{{ $item->SKU }}</td>
                        <td>{{ $item->ProductID }}</td>
                        <td>{{ $item->Stocks }}</td>
                        <td>{{ $item->Consign }}</td>
                        <td>
                            <span id="spr-text-{{ $item->id }}">{{ $item->SPR }}</span>
                            <form action="{{ route('store-inventory.update', $item->id) }}" method="POST" id="spr-form-{{ $item->id }}" style="display:none;">
                                @csrf
                                @method('PUT')
                                <input type="text" name="SPR" id="spr-input-{{ $item->id }}" value="{{ $item->SPR }}" class="form-control" style="width: 80px;">
                                <input type="hidden" name="store_id" value="{{ request()->get('store_id') }}"> <!-- Hidden store_id field -->
                                <button type="submit" class="btn btn-success btn-sm mt-1">Save</button>
                            </form>                            
                        </td>
                        <td>
                            <div class="action-container">
                                <div>
                                    <a href="{{ route('usc-returns.create', ['product_sku' => $item->SKU, 'store_id' => request()->get('store_id')]) }}" class="btn btn-primary">
                                        Request Return
                                    </a>
                                </div>
                                <div>
                                    <button id="edit-spr-btn-{{ $item->id }}" class="btn btn-warning" onclick="toggleEdit({{ $item->id }})">Edit Retail Price</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Link to JS and CSS with cache-busting version -->
        <script src="{{ asset('js/storeInv.js?v=8.0') }}"></script>
        <link rel="stylesheet" href="{{ asset('css/storeInv.css?v=8.0') }}">
    </div>
@endsection
