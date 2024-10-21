@extends('layout')

@section('title', 'POS')

@section('content')
    <div>
        <h1 class="page-title">Point of Sale</h1>
        
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

        <div class="action-select">
            <label for="actionSelect">Select Action:</label>
            <select id="actionSelect" name="action" onchange="updateSelectedAction()">
                <option value="price-check" {{ $selectedAction === 'price-check' ? 'selected' : '' }}>Price Check</option>
                <option value="pos" {{ $selectedAction === 'pos' ? 'selected' : '' }}>POS</option>
            </select>                       
        </div>        
        
        <form method="POST" action="{{ route('pos.index', ['store_id' => request()->input('store_id')]) }}" id="barcodeForm" onsubmit="updateSelectedAction()">
            @csrf
            <input type="hidden" name="action" id="actionInput" value="{{ $selectedAction }}">
            <input type="text" name="barcode_number" placeholder="Enter Barcode Number" required class="form-input">
            <button type="submit" class="form-button">Get Barcode Details</button>
        </form>                           

        <button id="scanBarcodeButton" class="form-button">Activate Camera For Barcode</button>

        <div id="cameraContainer" style="display:none; position:relative;">
            <div id="videoContainer"></div>
        </div>

        <div id="productDetails">
            @if(isset($productDetails))
                @if(is_array($productDetails) && isset($productDetails['message']))
                    <p class="message">{{ $productDetails['message'] }}</p>
                @endif
            @endif
        </div>

        <div id="storeInventoryDetails" style="{{ $selectedAction === 'pos' ? 'display: none;' : '' }}">
            @if(isset($storeInventoryDetails) && $storeInventoryDetails)
                <h2 class="inventory-title">Product Details</h2>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Stocks</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $storeInventoryDetails->ProductID }}</td>
                            <td>{{ $storeInventoryDetails->Stocks }}</td>
                            <td>{{ $storeInventoryDetails->SPR }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
        
        <div id="posCartDetails" style="{{ $selectedAction === 'price-check' ? 'display: none;' : '' }}">
            <h2 class="inventory-title">POS Cart</h2>
            @if(!empty($posCarts))
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Product SKU</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Sub Total</th>
                            <th>Action</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posCarts as $cart)
                            <tr>
                                <td>{{ $cart['product_sku'] }}</td>
                                <td>{{ $cart['quantity'] }}</td>
                                <td>{{ $cart['price'] }}</td>
                                <td>{{ $cart['sub_total'] }}</td>
                                <td>
                                    <form method="POST" action="{{ route('pos.void') }}">
                                        @csrf
                                        <input type="hidden" name="product_sku" value="{{ $cart['product_sku'] }}">
                                        <input type="hidden" name="store_id" value="{{ request()->input('store_id') }}">
                                        <button type="submit" class="void-button">Void</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right;"><strong>Total:</strong></td>
                            <td><strong>{{ number_format($posCarts->sum('sub_total'), 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Sale Form -->
                <h2 class="form-title">Complete Sale</h2>
                <form method="POST" action="{{ route('sales.store') }}" class="sale-form">
                    @csrf
                    <input type="hidden" name="ordered_items" value="{{ json_encode($posCarts) }}">
                    <input type="hidden" name="total" value="{{ $posCarts->sum('sub_total') }}"> <!-- Hidden field for total -->
                    <input type="hidden" name="store_id" value="{{ request()->input('store_id') }}"> <!-- Hidden field for store_id -->
                    
                    <div class="form-group">
                        <label for="amount_paid">Amount Paid:</label>
                        <input type="number" step="0.01" name="amount_paid" required class="form-input" placeholder="Enter Amount Paid" oninput="calculateChange()">
                    </div>
                    <div class="form-group">
                        <label for="cx_change">Change:</label>
                        <input type="number" step="0.01" name="cx_change" class="form-input" placeholder="Change" readonly id="cx_change">
                    </div>
                    <button type="submit" class="form-button">Complete Sale</button>
                </form>                              
            @else
                <p>No items in the cart.</p>
            @endif
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
        <script src="{{ asset('js/pos.js?v=4.3') }}"></script>
        <script>
            // Pass PHP values to JavaScript variables
            const totalAmount = {{ json_encode($posCarts->sum('sub_total')) }};
        </script>     
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/pos.css?v=4.3') }}">
@endsection
