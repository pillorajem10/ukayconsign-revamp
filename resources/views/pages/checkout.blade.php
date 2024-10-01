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

<div class="checkout-page-cont">
    <div class="checkout-page-cart-summary">
        <h2>Order Summary</h2>
        @if($carts->isEmpty())
            <p>Your cart is empty.</p>
        @else
            <table class="checkout-page-cart-summary-table">
                <thead>
                    <tr>
                        <th>Bundle</th>
                        <th>Quantity</th>
                        <th>Price/Pc</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @foreach($carts as $cart)
                        @php
                            $subtotal = $cart->price * $cart->quantity;
                            $grandTotal += $subtotal;
                        @endphp
                        <tr>
                            <td>{{ $cart->product->Bundle }}</td>
                            <td>{{ $cart->quantity }}</td>
                            <td>₱{{ number_format($cart->price, 2) }}</td>
                            <td>₱{{ number_format($subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                        <td><strong>₱{{ number_format($grandTotal, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        @endif
        <button type="button" class="checkout-page-btn">Proceed to Place Order</button>
    </div>
</div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endsection
