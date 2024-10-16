@extends('layout')

@section('title', 'Add Instant Buy Product')

@section('content')
<div>
    <h1 class="add-product-title">Add Instant Buy Product</h1>

    {{-- Display success and error messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('instant_buy_products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-row">
            <div class="form-column">
                <div class="form-group">
                    <label for="product_sku">Select Product</label>
                    <select name="product_sku" id="product_sku" class="form-control" required>
                        <option value="" disabled selected>Select a product</option>
                        @foreach($storeInventory as $item)
                            <option value="{{ $item->SKU }}">{{ $item->ProductID }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="product_barcode">Product Barcode</label>
                    <input type="text" name="product_barcode" id="product_barcode" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="size">Size</label>
                    <input type="text" name="size" id="size" class="form-control">
                </div>

                <div class="form-group">
                    <label for="issue">Issue</label>
                    <input type="text" name="issue" id="issue" class="form-control">
                </div>
            </div>

            <div class="form-column">
                <div class="form-group">
                    <label for="dimension">Dimension</label>
                    <input type="text" name="dimension" id="dimension" class="form-control">
                </div>

                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="images">Images (Max 6)</label>
                    <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                </div>

                <div class="form-group">
                    <label for="model">Model</label>
                    <input type="text" name="model" id="model" class="form-control">
                </div>

                <div class="form-group">
                    <label for="video">Video</label>
                    <input type="file" name="video" id="video" class="form-control" accept="video/*">
                </div>
            </div>
        </div>

        <input type="hidden" name="store_id" value="{{ $storeId }}"> <!-- Hidden input for store_id -->

        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
</div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/instantBuyAdd.css?v=3.7') }}">
@endsection
