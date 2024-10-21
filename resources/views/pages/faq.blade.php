@extends('layout')

@section('title', 'How to use USC')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4 title">How to use USC</h1>
        <div class="manual-content">
            <h2 class="subtitle">How to Order</h2>
            <ol class="order-steps">
                <li class="step">
                    <strong class="step-title">Login or Register:</strong> First, log in to your account or register if you don't have one.
                </li>
                <li class="step">
                    <strong class="step-title">Select Desired Bundle:</strong> Browse through our selection and choose the bundle you want to purchase.
                    <ul class="badge-info">
                        <li>For <strong>Silver Badges</strong>, you can add a maximum of 3 bundles.</li>
                        <li>For <strong>Gold Badges</strong>, you can add a maximum of 5 bundles.</li>
                        <li>For <strong>Platinum Badges</strong>, you can add unlimited bundles.</li>
                    </ul>
                </li>
                <li class="step">
                    <strong class="step-title">Proceed to Checkout:</strong> Once you have selected your bundles, click the "Proceed to Checkout" button.
                </li>
                <li class="step">
                    <strong class="step-title">Fill Out the Checkout Form:</strong> On the checkout page, fill out all the required fields in the form, then click "Proceed to Place Order."
                </li>
                <li class="step">
                    <strong class="step-title">Receive Confirmation:</strong> You will receive an email with your order details, and you can check the Orders page to view your orders.
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/faq.css?v=4.3') }}">
@endsection
