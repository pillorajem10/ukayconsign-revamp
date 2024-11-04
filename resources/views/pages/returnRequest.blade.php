@extends('layout')

@section('title', 'Request Return')

@section('content')
    <div>
        <h1>Request Return</h1>

        <form action="{{ route('usc-returns.store') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
            
            <!-- Product SKU -->
            <div class="form-group">
                <label for="product_sku">Product SKU:</label>
                <input type="hidden" name="product_sku" id="product_sku" value="{{ $productSku }}">
                <input type="text" value="{{ $productName }}" class="form-control" disabled>
            </div>

            <!-- Store ID and Store Name -->
            <div class="form-group">
                <label for="store_id">Store:</label>
                <input type="hidden" name="store_id" id="store_id" value="{{ $storeId }}">
                <input type="text" value="{{ $storeName }}" class="form-control" disabled>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" required min="1" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Submit Return Request</button>
        </form>
    </div>
@endsection
