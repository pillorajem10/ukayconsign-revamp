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

        <div class="action-select">
            <label for="actionSelect">Select Action:</label>
            <select id="actionSelect" name="action" onchange="updateSelectedAction()">
                <option value="price-check" {{ $selectedAction === 'price-check' ? 'selected' : '' }}>Price Check</option>
                <option value="pos" {{ $selectedAction === 'pos' ? 'selected' : '' }}>POS</option>
            </select>                       
        </div>        
        
        <form method="POST" action="{{ route('pos.index') }}" id="barcodeForm" onsubmit="updateSelectedAction()">
            @csrf
            <input type="hidden" name="action" id="actionInput" value="{{ $selectedAction }}">
            <select name="store_id" required class="form-select">
                <option value="">Select Store</option>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                @endforeach
            </select>
            <input type="text" name="barcode_number" placeholder="Enter Barcode Number" required class="form-input">
            <button type="submit" class="form-button">Get Barcode Details</button>
        </form>                           

        <button id="scanBarcodeButton" class="form-button">Scan Barcode</button>

        <div id="cameraContainer" style="display:none; position:relative;">
            <div id="videoContainer" style="width: 100%; height: auto;"></div>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posCarts as $cart)
                            <tr>
                                <td>{{ $cart['product_sku'] }}</td>
                                <td>{{ $cart['quantity'] }}</td>
                                <td>{{ $cart['price'] }}</td>
                                <td>{{ $cart['sub_total'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                            <td><strong>{{ number_format($posCarts->sum('sub_total'), 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            

                <!-- Sale Form -->
                <h2 class="form-title">Complete Sale</h2>
                <form method="POST" action="{{ route('sales.store') }}" class="sale-form">
                    @csrf
                    <input type="hidden" name="ordered_items" value="{{ json_encode($posCarts) }}">
                    
                    <div class="form-group">
                        <label for="customer_name">Customer Name:</label>
                        <input type="text" name="customer_name" required class="form-input" placeholder="Enter Customer Name">
                    </div>
                    <div class="form-group">
                        <label for="customer_number">Customer Number:</label>
                        <input type="text" name="customer_number" required class="form-input" placeholder="Enter Customer Number">
                    </div>
                    <div class="form-group">
                        <label for="total">Total:</label>
                        <input type="number" step="0.01" name="total" required class="form-input" value="{{ number_format($posCarts->sum('sub_total'), 2) }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="mode_of_payment">Mode of Payment:</label>
                        <input type="text" name="mode_of_payment" required class="form-input" placeholder="Enter Mode of Payment">
                    </div>
                    <div class="form-group">
                        <label for="amount_paid">Amount Paid:</label>
                        <input type="number" step="0.01" name="amount_paid" required class="form-input" placeholder="Enter Amount Paid" oninput="calculateChange()">
                    </div>
                    <div class="form-group">
                        <label for="cx_change">Change:</label>
                        <input type="number" step="0.01" name="cx_change" class="form-input" placeholder="Enter Change" readonly id="cx_change">
                    </div>
                    <div class="form-group">
                        <label for="cx_type">Change Type:</label>
                        <input type="text" name="cx_type" class="form-input" placeholder="Enter Change Type">
                    </div>
                    <button type="submit" class="form-button">Complete Sale</button>
                </form>
            @else
                <p>No items in the cart.</p>
            @endif
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
        <script src="{{ asset('js/pos.js?v=1.6') }}"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/pos.css?v=1.6') }}">
@endsection
