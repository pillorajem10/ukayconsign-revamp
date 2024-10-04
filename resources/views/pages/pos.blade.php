@extends('layout')

@section('title', 'Point of Sale')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pos.css') }}">
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

    <div id="cameraContainer" style="display:none;">
        <div id="videoContainer"></div>
        <button id="stopScan" class="form-button">Stop Scanning</button>
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
</div>

<script>
    document.getElementById('scanBarcodeButton').addEventListener('click', function() {
        // Start the camera scanning
        document.getElementById('cameraContainer').style.display = 'block';
        Quagga.init({
            inputStream : {
                name : "Live",
                type : "LiveStream",
                target: document.getElementById('videoContainer'),    // In case of a live stream
                constraints: {
                    facingMode: "environment" // Use the back camera
                },
            },
            decoder : {
                readers : ["code_128_reader"] // Add the barcode types you want to read
            },
        }, function(err) {
            if (err) {
                console.log(err);
                return;
            }
            Quagga.start();
        });

        Quagga.onDetected(function(data) {
            var code = data.codeResult.code;
            // Fill in the barcode input and submit the form
            document.querySelector('input[name="barcode_number"]').value = code;
            Quagga.stop();
            document.getElementById('cameraContainer').style.display = 'none';
            document.getElementById('barcodeForm').submit();
        });
    });

    document.getElementById('stopScan').addEventListener('click', function() {
        Quagga.stop();
        document.getElementById('cameraContainer').style.display = 'none';
    });
</script>
@endsection
