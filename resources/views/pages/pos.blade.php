@extends('layout')

@section('title', 'Point of Sale')

@section('content')
    <div>
        <h1 class="page-title">Point of Sale</h1>
        <form method="POST" action="{{ route('pos.index') }}" id="barcodeForm">
            @csrf
            <select name="store_id" required class="form-select">
                <option value="">Select Store</option>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                @endforeach
            </select>
            <input type="text" name="barcode_number" placeholder="Enter Barcode Number" required class="form-input">
            <button type="submit" class="form-button">Search</button>
        </form>

        <button id="scanBarcodeButton" class="form-button">Scan Barcode</button>

        <div id="cameraContainer" style="display:none; position:relative;">
            <div id="videoContainer" style="width: 100%; height: auto;"></div>
        </div>

        <div id="productDetails">
            @if(isset($productDetails))
                @if(is_array($productDetails) && isset($productDetails['message']))
                    <p class="message">{{ $productDetails['message'] }}</p>
                @else
                    <p>SKU: {{ $productDetails->SKU }}</p>
                @endif
            @endif
        </div>

        <div id="storeInventoryDetails">
            @if(isset($storeInventoryDetails) && $storeInventoryDetails->isNotEmpty())
                <h2 class="inventory-title">Store Inventory Details</h2>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Stocks</th>
                            <th>Consign</th>
                            <th>SPR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($storeInventoryDetails as $inventory)
                            <tr>
                                <td>{{ $inventory->ProductID }}</td>
                                <td>{{ $inventory->Stocks }}</td>
                                <td>{{ $inventory->Consign }}</td>
                                <td>{{ $inventory->SPR }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif(isset($storeInventoryDetails))
                <p>No inventory found for the selected store and SKU.</p>
            @endif
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
        <script src="{{ asset('js/pos.js?v=1.5') }}"></script> <!-- Include the separate JS file -->
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/pos.css?v=1.5') }}">
@endsection