@extends('layout')

@section('title', 'Your Cart')

@section('content')
    <a href="{{ route('home') }}" class="checkout-page-link">Go back to home page</a>
    <div class="cart-container">
        <table class="cart-page-table">
            <thead class="cart-page-header">
                <tr>
                    <th class="cart-page-select">Select</th>
                    <th class="cart-page-select">Image</th>
                    <th class="cart-page-product-sku">Bundle</th>
                    <th class="cart-page-category">Product Category</th> 
                    <th class="cart-page-price">Price/Pc</th> 
                    <th class="cart-page-quantity">Quantity</th>
                    <th class="cart-page-srp">SRP</th>
                    <th class="cart-page-subtotal">Sub Total</th>
                </tr>
            </thead>
            <tbody class="cart-page-body">
                @if($carts->isEmpty())
                    <tr>
                        <td colspan="8" class="cart-view-empty-message">Your Cart Is Empty</td>
                    </tr>
                @else
                    @php
                        $grandTotal = 0; // Initialize grand total
                    @endphp
                    @foreach($carts as $cart)
                    @php
                        $subtotal = $cart->price * $cart->quantity; // Calculate subtotal for the item
                        $grandTotal += $subtotal; // Add to grand total
                        $imageData = base64_encode($cart->product->Image); // Encode binary data to base64
                        $imageType = 'image/png'; // Adjust based on your image type (e.g., image/jpeg, image/png, etc.)
                    @endphp
                    <tr class="cart-view-item" data-id="{{ $cart->id }}">
                        <td>
                            <input type="checkbox" class="bundle-checkbox" data-bundle="{{ $cart->product->Bundle }}" onchange="updateButtonState()" /> <!-- Checkbox -->
                        </td>
                        <td>
                            <img src="data:{{ $imageType }};base64,{{ $imageData }}" alt="{{ $cart->product->Bundle }}" style="width: 50px; height: auto; border-radius: 5px;" />
                        </td>
                        <td class="cart-view-product-sku-value">{{ $cart->product->Bundle }}</td>
                        <td class="cart-view-category-value">{{ $cart->product->Category ?? 'N/A' }}</td> 
                        <td class="cart-view-price-value">₱{{ number_format($cart->price, 2) }}</td>
                        <td class="cart-view-quantity-value">{{ $cart->quantity }}</td>
                        <td class="cart-view-srp-value">{{ $cart->product->SRP ?? 'N/A' }}</td> 
                        <td class="cart-view-subtotal-value">₱{{ number_format($subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>                      
            <tfoot>
                <tr>
                    <td colspan="7" style="text-align: right; font-weight: bold;">Total:</td>
                    <td class="cart-page-grand-total">₱{{ number_format($grandTotal, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="8" style="text-align: right;">
                        @if(!$carts->isEmpty())
                            <button id="delete-button" class="btn btn-danger" onclick="deleteCheckedItems()" disabled>Delete Selected</button>
                            <button id="checkout-button" class="btn btn-primary" onclick="proceedToCheckout()">Place Consignment</button>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    

    <div class="small-screen-cart-container">
        @if($carts->isNotEmpty())
            @php
                $grandTotal = 0; // Initialize grand total
            @endphp
            @foreach($carts as $cart)
                @php
                    $subtotal = $cart->price * $cart->quantity; // Calculate subtotal for the item
                    $grandTotal += $subtotal; // Add to grand total
                    $imageData = base64_encode($cart->product->Image); // Encode binary data to base64
                    $imageType = 'image/png'; // Adjust based on your image type
                @endphp
                <div class="small-screen-cart-card" data-id="{{ $cart->id }}">
                    <div class="small-screen-cart-select">
                        <input type="checkbox" class="small-screen-bundle-checkbox" data-bundle="{{ $cart->product->Bundle }}" onchange="updateButtonState()" />
                    </div>
                    <div class="small-screen-cart-image">
                        <img src="data:{{ $imageType }};base64,{{ $imageData }}" alt="{{ $cart->product->Bundle }}" style="width: 100px; height: auto; border-radius: 5px; margin-right: 10px;" />
                    </div>
                    <div class="small-screen-cart-details">
                        <div class="small-screen-cart-product-sku"><strong>Bundle:</strong> {{ $cart->product->Bundle }}</div>
                        <div class="small-screen-cart-category"><strong>Category:</strong> {{ $cart->product->Category ?? 'N/A' }}</div>
                        <div class="small-screen-cart-price"><strong>Price/pc:</strong> ₱{{ number_format($cart->price, 2) }}</div>
                        <div class="small-screen-cart-quantity"><strong>Quantity:</strong> {{ $cart->quantity }}</div>
                        <div class="small-screen-cart-srp"><strong>SRP:</strong> {{ $cart->product->SRP ?? 'N/A' }}</div>
                        <div class="small-screen-cart-subtotal"><strong>Sub Total:</strong> ₱{{ number_format($subtotal, 2) }}</div>
                    </div>
                </div>
            @endforeach
    
            <div class="small-screen-cart-total">
                <strong>Total:</strong> ₱{{ number_format($grandTotal, 2) }}
            </div>
    
            <div class="small-screen-cart-actions">
                <button id="delete-button-small" class="btn btn-danger" onclick="deleteCheckedItemsSmallScreen()" disabled>Delete Selected</button>
                <button id="checkout-button-small" class="btn btn-primary" onclick="proceedToCheckout()">Place Consignment</button>
            </div>
        @else
            <div class="small-screen-cart-login-message">Please log in to view your cart.</div>
        @endif
    </div>
     

    <!-- Include the cart.js file -->
    <script src="{{ asset('js/cart.js?v=4.5') }}"></script>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/cartPage.css?v=4.5') }}">
@endsection
