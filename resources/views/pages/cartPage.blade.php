@extends('layout')

@section('title', 'Your Cart')

@section('content')
    <div class="cart-container">
        <h1>Your Shopping Cart</h1>
        
        <table class="cart-page-table">
            <thead class="cart-page-header">
                <tr>
                    <th class="cart-page-select">Select</th>
                    <th class="cart-page-product-sku">Bundle</th>
                    <th class="cart-page-category">Product Category</th> 
                    <th class="cart-page-price">Price/Pc</th> 
                    <th class="cart-page-quantity">Quantity</th>
                    <th class="cart-page-srp">SRP</th>
                    <th class="cart-page-subtotal">Sub Total</th>
                </tr>
            </thead>
            <tbody class="cart-page-body">
                @if(Auth::check())
                    @php
                        $grandTotal = 0; // Initialize grand total
                    @endphp
                    @foreach($carts as $cart)
                        @php
                            $subtotal = $cart->price * $cart->quantity; // Calculate subtotal for the item
                            $grandTotal += $subtotal; // Add to grand total
                        @endphp
                        <tr class="cart-page-item" data-id="{{ $cart->id }}">
                            <td>
                                <input type="checkbox" class="bundle-checkbox" data-bundle="{{ $cart->product->Bundle }}" onchange="updateButtonState()" /> <!-- Checkbox -->
                            </td>                    
                            <td class="cart-page-product-sku-value">{{ $cart->product->Bundle }}</td>
                            <td class="cart-page-category-value">{{ $cart->product->Category ?? 'N/A' }}</td> 
                            <td class="cart-page-price-value">₱{{ number_format($cart->price, 2) }}</td>
                            <td class="cart-page-quantity-value">{{ $cart->quantity }}</td>
                            <td class="cart-page-srp-value">{{ $cart->product->maxSRP ?? 'N/A' }}</td> 
                            <td class="cart-page-subtotal-value">₱{{ number_format($subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="cart-page-login-message" style="text-align: center; color: red;">Please log in to view your cart.</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right; font-weight: bold;">Total:</td>
                    <td class="cart-page-grand-total">
                        @if(Auth::check())
                            ₱{{ number_format($grandTotal, 2) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align: right;">
                        @if(Auth::check())
                            <button id="delete-button" class="btn btn-danger" onclick="deleteCheckedItems()" disabled>Delete Selected</button>
                            <button id="checkout-button" class="btn btn-primary" onclick="proceedToCheckout()" disabled>Proceed to Checkout</button>
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="small-screen-cart-container">
        <h1>Your Shopping Cart</h1>
        @if(Auth::check() && $carts->isNotEmpty())
            @php
                $grandTotal = 0; // Initialize grand total
            @endphp
            @foreach($carts as $cart)
                @php
                    $subtotal = $cart->price * $cart->quantity; // Calculate subtotal for the item
                    $grandTotal += $subtotal; // Add to grand total
                @endphp
                <div class="small-screen-cart-card" data-id="{{ $cart->id }}">
                    <div class="small-screen-cart-select">
                        <input type="checkbox" class="small-screen-bundle-checkbox" data-bundle="{{ $cart->product->Bundle }}" onchange="updateButtonState()" />
                    </div>
                    <div class="small-screen-cart-product-sku">Bundle: {{ $cart->product->Bundle }}</div>
                    <div class="small-screen-cart-category">Category: {{ $cart->product->Category ?? 'N/A' }}</div>
                    <div class="small-screen-cart-price">Price/Pc: ₱{{ number_format($cart->price, 2) }}</div>
                    <div class="small-screen-cart-quantity">Quantity: {{ $cart->quantity }}</div>
                    <div class="small-screen-cart-srp">SRP: {{ $cart->product->maxSRP ?? 'N/A' }}</div>
                    <div class="small-screen-cart-subtotal">Sub Total: ₱{{ number_format($subtotal, 2) }}</div>
                </div>
            @endforeach
    
            <div class="small-screen-cart-total">
                <strong>Total:</strong> ₱{{ number_format($grandTotal, 2) }}
            </div>
    
            <div class="small-screen-cart-actions">
                <button id="delete-button-small" class="btn btn-danger" onclick="deleteCheckedItemsSmallScreen()" disabled>Delete Selected</button>
                <button id="checkout-button-small" class="btn btn-primary" onclick="proceedToCheckout()" disabled>Proceed to Checkout</button>
            </div>
        @else
            <div class="small-screen-cart-login-message" style="text-align: center; color: red;">Please log in to view your cart.</div>
        @endif
    </div>
    

    <!-- Include the cart.js file -->
    <script src="{{ asset('js/cart.js?v=1.1') }}"></script>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/cartPage.css?v=1.1') }}">
@endsection
