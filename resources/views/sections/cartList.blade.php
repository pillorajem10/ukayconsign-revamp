<body>
    <table class="cart-view-table">
        <thead class="cart-view-header">
            <tr>
                <th class="cart-view-select">Select</th>
                <th class="cart-view-product-sku">Bundle</th>
                <th class="cart-view-category">Product Category</th> 
                <th class="cart-view-price">Price/Pc</th> 
                <th class="cart-view-quantity">Quantity</th>
                <th class="cart-view-srp">SRP</th>
                <th class="cart-view-subtotal">Sub Total</th>
            </tr>
        </thead>
        <tbody class="cart-view-body">
            @if(Auth::check())
                @php
                    $grandTotal = 0; // Initialize grand total
                @endphp
                @foreach($carts as $cart)
                @php
                    $subtotal = $cart->price * $cart->quantity; // Calculate subtotal for the item
                    $grandTotal += $subtotal; // Add to grand total
                @endphp
                <tr class="cart-view-item" data-id="{{ $cart->id }}">
                    <td>
                        <input type="checkbox" class="bundle-checkbox" data-bundle="{{ $cart->product->Bundle }}" onchange="updateButtonState()" /> <!-- Checkbox -->
                    </td>                    
                    <td class="cart-view-product-sku-value">{{ $cart->product->Bundle }}</td>
                    <td class="cart-view-category-value">{{ $cart->product->Category ?? 'N/A' }}</td> 
                    <td class="cart-view-price-value">₱{{ number_format($cart->price, 2) }}</td>
                    <td class="cart-view-quantity-value">{{ $cart->quantity }}</td>
                    <td class="cart-view-srp-value">{{ $cart->product->SRP ?? 'N/A' }}</td> 
                    <td class="cart-view-subtotal-value">₱{{ number_format($subtotal, 2) }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="cart-view-login-message" style="text-align: center; color: red;">Please log in to view your cart.</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align: right; font-weight: bold;">Total:</td>
                <td class="cart-view-grand-total">
                    @if(Auth::check())
                        ₱{{ number_format($grandTotal, 2) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="7" style="text-align: right;">
                    @if(Auth::check())
                        <button id="delete-button" class="btn btn-danger" onclick="deleteCheckedItems()" disabled>Delete Selected</button>
                        <button id="checkout-button" class="btn btn-primary" onclick="proceedToCheckout()">Proceed to Checkout</button>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Include the cart.js file -->
    <script src="{{ asset('js/cart.js?v=2.6') }}"></script>
</body>
