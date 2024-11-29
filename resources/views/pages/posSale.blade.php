@extends('layout')

@section('title', 'POS Sale')

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
        
        <form method="POST" action="{{ route('posSale.index', ['store_id' => request()->input('store_id')]) }}" id="barcodeForm" onsubmit="updateSelectedAction()">
            @csrf
            <input type="hidden" name="action" id="actionInput" value="{{ $selectedAction }}">
            <input type="text" name="barcode_number" id="barcodeNumberField" placeholder="Enter Barcode Number" required class="form-input">
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
                <div>
                    <!-- Add a select field for discount type -->
                    <label for="discount-type">Discount Type:</label>
                    <select id="discount-type" class="discount-type">
                        <option value="amount" selected>Discount By Amount</option>
                        <option value="percent">Discount By Percent</option>
                    </select>
                </div>
                
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Original Total</th>
                            <th>Discount</th>
                            <th>Sub Total</th>
                            <th>Action</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posCarts as $cart)
                            <tr>
                                <td>{{ $cart['product_bundle_id'] }}</td>
                                <td>{{ $cart['quantity'] }}</td>
                                <td>{{ number_format($cart['price'], 2) }}</td>
                                <td>{{ number_format($cart['orig_total'], 2) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('posSale.applyDiscount') }}" class="discount-form">
                                        @csrf
                                        <input type="hidden" name="product_sku" value="{{ $cart['product_sku'] }}">
                                        <input type="hidden" name="store_id" value="{{ request()->input('store_id') }}">
                
                                        <!-- Dynamic discount input area -->
                                        <div class="discount-input-container">
                                            <input type="number" 
                                                name="discount" 
                                                step="0.01" 
                                                value="{{ number_format($cart['discount'], 2) }}" 
                                                class="form-input discount-input" 
                                                placeholder="Enter Discount" 
                                                style="display: none;">
                                            
                                            <select name="discount_percent" 
                                                    class="form-input discount-percent" 
                                                    style="display: none;">
                                                <option value="">Select Percent</option>
                                                @foreach([50, 40, 35, 30, 25, 20, 15, 10] as $percent)
                                                    <option value="{{ $percent }}" 
                                                            {{ old('discount_percent', $cart['discount']) == $percent ? 'selected' : '' }}>
                                                        {{ $percent }}%
                                                    </option>
                                                @endforeach
                                            </select>
                                        
                                        </div>
                
                                        <button type="submit" class="apply-discount-button">Apply</button>
                                    </form>
                                </td>                                
                                <td>{{ number_format($cart['sub_total'], 2) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('posSale.void') }}">
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
                            <td colspan="6" style="text-align: right;"><strong>Total:</strong></td>
                            <td><strong>{{ number_format($posCarts->sum('sub_total'), 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            
        
                <!-- Sale Form -->
                <h2 class="form-title">Complete Sale</h2>
                <form method="POST" action="{{ route('salesPrelove.store') }}" class="sale-form">
                    @csrf
                    <input type="hidden" name="ordered_items" value="{{ json_encode($posCarts) }}">
                    <input type="hidden" name="total" value="{{ $posCarts->sum('sub_total') }}">
                    <input type="hidden" name="store_id" value="{{ request()->input('store_id') }}">
                    <input type="hidden" name="original_total" value="{{ $posCarts->sum('orig_total') }}"> <!-- Hidden field for total of Original Totals -->
                    <input type="hidden" name="discount" value="{{ $posCarts->sum('discount') }}"> <!-- Hidden field for total of Discounts -->
                    
                    <!-- Fields Container 1: Customer Details -->
                    <div class="form-cont">
                        <div class="fields-container">
                            <div class="form-group">
                                <label for="customer_name">Customer Name:</label>
                                <input type="text" name="customer_name" class="form-input" placeholder="Enter Customer Name (Optional)">
                            </div>
                            
                            <div class="form-group">
                                <label for="customer_number">Customer Phone #:</label>
                                <input type="text" name="customer_number" class="form-input" placeholder="Enter Customer Phone # (Optional)">
                            </div>
                            
                            <div class="form-group">
                                <label for="customer_email">Customer Email:</label>
                                <input type="email" name="customer_email" class="form-input" placeholder="Enter Customer Email (Optional)">
                            </div>
                    
                            <div class="form-group">
                                <label for="cx_type">Customer Type:</label>
                                <select name="cx_type" class="form-select" id="cx_type">
                                    <option value="User" selected>User</option>
                                    <option value="Re-Seller">Re-Seller</option>
                                </select>
                            </div>
                        </div>
                    
                        <!-- Fields Container 2: Payment Details -->
                        <div class="fields-container">
                            <!-- Platform Selection -->
                            <div class="form-group">
                                <label for="platform">Platform:</label>
                                <select name="platform" class="form-select" id="platform">
                                    <option value="Physical Store" selected>Physical Store</option>
                                    <option value="FB Live">FB Live</option>
                                    <option value="Carousel">Carousel</option>
                                    <option value="FB Market Place">FB Market Place</option>
                                    <option value="Interest">Interest</option>
                                </select>
                            </div>
                            
                            <!-- Mode of Payment -->
                            <div class="form-group">
                                <label for="mode_of_payment">Mode of Payment:</label>
                                <select name="mode_of_payment" class="form-select" id="mode_of_payment">
                                    <option value="Cash" selected>Cash</option>
                                    <option value="eWallet">eWallet</option>
                                    <option value="Interest">Interest</option>
                                </select>
                            </div>
                            
                            <!-- Reference Number for eWallet -->
                            <div class="form-group" id="ref_number_group">
                                <label for="ref_number_ewallet">Ref #:</label>
                                <input type="text" name="ref_number_ewallet" class="form-input" placeholder="Enter Reference Number">
                            </div>
                            
                            <!-- Amount Paid -->
                            <div class="form-group" id="amount_paid_group">
                                <label for="amount_paid">Amount Paid:</label>
                                <input type="number" step="0.01" name="amount_paid" class="form-input" placeholder="Enter Amount Paid" oninput="calculateChange()">
                            </div>
                            
                            <!-- Change -->
                            <div class="form-group" id="change_group">
                                <label for="cx_change">Change:</label>
                                <input type="number" step="0.01" name="cx_change" class="form-input" placeholder="Change" readonly id="cx_change">
                            </div>
                            
                            <!-- Interest Field -->
                            <div class="form-group" id="interest_group">
                                <label for="interest">Interest:</label>
                                <select name="interest" class="form-input">
                                    <option value="" selected disabled>Select Customer's Interest (Optional)</option>
                                    <option value="Shoes">Shoes</option>
                                    <option value="Shirts">Shirts</option>
                                    <option value="Caps">Caps</option>
                                    <option value="Bags">Bags</option>
                                    <option value="Hoodies">Hoodies</option>
                                    <option value="Shorts">Shorts</option>
                                    <option value="Pants">Pants</option>
                                </select>
                            </div>
                            
                            
                            <!-- Remarks Field -->
                            <div class="form-group" id="remarks_group">
                                <label for="remarks">Remarks:</label>
                                <input type="text" name="remarks" class="form-input" placeholder="Enter any Remarks (Optional)">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="form-button">Complete Sale</button>
                </form>
                                                        
            @else
                <p>No items in the cart.</p>
            @endif
        </div>        
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
        <script src="{{ asset('js/posSale.js?v=8.0') }}"></script>
        <script>
            // Pass PHP values to JavaScript variables
            const totalAmount = {{ json_encode($posCarts->sum('sub_total')) }};
        </script>     
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/posSale.css?v=8.0') }}">
@endsection
