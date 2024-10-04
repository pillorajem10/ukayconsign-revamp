@extends('layout')

@section('title', 'Checkout | Ukay Supplier Consign')

@section('content')
    @if(session('success'))
        <div id="success-message" class="checkout-page-alert checkout-page-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="error-message" class="checkout-page-alert checkout-page-alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="checkout-loading-overlay" id="checkoutLoadingOverlay">
        <div class="checkout-spinner"></div>
    </div>

    <a href="{{ route('home') }}" class="checkout-page-link">Go back to home page</a>
    <div class="checkout-page-cont">
        <div class="checkout-page-form-container">
            <form action="{{ route('checkout.store') }}" method="POST" class="checkout-page-form" onsubmit="showLoading()">
                @csrf
                <h2 class="checkout-page-form-title">Customer Information</h2>
                
                <div class="checkout-page-form-group">
                    <label for="first_name" class="checkout-page-label">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="checkout-page-input" value="{{ $latestOrder->first_name ?? old('first_name') }}" required>
                </div>
                <div class="checkout-page-form-group">
                    <label for="last_name" class="checkout-page-label">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="checkout-page-input" value="{{ $latestOrder->last_name ?? old('last_name') }}" required>
                </div>
                <div class="checkout-page-form-group">
                    <label for="address" class="checkout-page-label">Address</label>
                    <input type="text" name="address" id="address" class="checkout-page-input" value="{{ $latestOrder->address ?? old('address') }}" required>
                </div>
                
                <h2 class="checkout-page-form-title">Store Information</h2>
                
                <div class="checkout-page-form-group">
                    <label for="store_name" class="checkout-page-label">Store Name</label>
                    <input type="text" name="store_name" id="store_name" class="checkout-page-input" value="{{ $store->store_name ?? '' }}" required>
                </div>
                <div class="checkout-page-form-group">
                    <label for="store_address" class="checkout-page-label">Store Address</label>
                    <input type="text" name="store_address" id="store_address" class="checkout-page-input" value="{{ $store->store_address ?? '' }}" required>
                </div>
                <div class="checkout-page-form-group">
                    <label for="store_phone_number" class="checkout-page-label">Store Phone Number</label>
                    <input type="text" name="store_phone_number" id="store_phone_number" class="checkout-page-input" value="{{ $store->store_phone_number ?? '' }}" required>
                </div>
                <div class="checkout-page-form-group">
                    <label for="store_email" class="checkout-page-label">Store Email</label>
                    <input type="email" name="store_email" id="store_email" class="checkout-page-input" value="{{ $store->store_email ?? '' }}" required>
                </div>
                
                <button type="submit" class="checkout-page-btn">Proceed to Place Order</button>
            </form>

            <div class="checkout-page-cart-summary">
                <h2 class="checkout-page-summary-title">Order Summary</h2>
                @if($carts->isEmpty())
                    <p class="checkout-page-empty-cart">Your cart is empty.</p>
                @else
                    <table class="checkout-page-cart-summary-table">
                        <thead>
                            <tr>
                                <th class="checkout-page-summary-header">Bundle</th>
                                <th class="checkout-page-summary-header">Category</th>
                                <th class="checkout-page-summary-header">Quantity</th>
                                <th class="checkout-page-summary-header">Price/Pc</th>
                                <th class="checkout-page-summary-header">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @foreach($carts as $cart)
                                @php
                                    $subtotal = $cart->price * $cart->quantity;
                                    $grandTotal += $subtotal;
                                @endphp
                                <tr class="checkout-page-summary-row">
                                    <td class="checkout-page-summary-data">{{ $cart->product->Bundle }}</td>
                                    <td class="checkout-page-summary-data">{{ $cart->product->Category }}</td>
                                    <td class="checkout-page-summary-data">{{ $cart->quantity }}</td>
                                    <td class="checkout-page-summary-data">₱{{ number_format($cart->price, 2) }}</td>
                                    <td class="checkout-page-summary-data">₱{{ number_format($subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="checkout-page-summary-footer">
                                <td colspan="4" style="text-align: right;"><strong>Total:</strong></td>
                                <td><strong>₱{{ number_format($grandTotal, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        </div>
        <script src="{{ asset('js/checkout.js?v=1.9') }}"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout.css?v=1.9') }}">
@endsection
