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

    @if ($errors->any())
        <div class="checkout-page-alert checkout-page-alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="checkout-loading-overlay" id="checkoutLoadingOverlay">
        <div class="checkout-spinner"></div>
    </div>

    <a href="{{ route('home') }}" class="checkout-page-link">Go back to home page</a>
    <div class="checkout-page-cont">
        <div class="checkout-page-form-container">
            <form action="{{ route('checkout.store') }}" method="POST" class="checkout-page-form" enctype="multipart/form-data" onsubmit="showLoading()">
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

                @if(!Auth::check())
                    <div class="checkout-page-form-group">
                        <label for="email" class="checkout-page-label">Create Your New Email</label>
                        <input type="email" name="email" id="email" class="checkout-page-input" value="{{ old('email') }}" required>
                    </div>
                    <div class="checkout-page-form-group">
                        <label for="password" class="checkout-page-label">Create Your New Password</label>
                        <input type="password" name="password" id="password" class="checkout-page-input" required>
                    </div>
                    <div class="checkout-page-form-group">
                        <label for="fb_link" class="checkout-page-label">Facebook Link</label>
                        <input type="url" name="fb_link" id="fb_link" class="checkout-page-input" value="{{ old('fb_link') }}">
                    </div>
                    <div class="checkout-page-form-group">
                        <label for="phone_number" class="checkout-page-label">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" class="checkout-page-input" value="{{ old('phone_number') }}" required>
                    </div>
                    <div class="checkout-page-form-group">
                        <label for="government_id_card" class="checkout-page-label">Government ID</label>
                        <input type="file" name="government_id_card" id="government_id_card" class="checkout-page-input" required>
                    </div>
                    <div class="checkout-page-form-group">
                        <label for="proof_of_billing" class="checkout-page-label">Proof of Billing</label>
                        <input type="file" name="proof_of_billing" id="proof_of_billing" class="checkout-page-input" required>
                    </div>
                    <div class="checkout-page-form-group">
                        <label for="selfie_uploaded" class="checkout-page-label">Upload Your Selfie</label>
                        <input type="file" name="selfie_uploaded" id="selfie_uploaded" class="checkout-page-input" required>
                    </div>
                    <div class="checkout-page-form-group">
                        <label for="estimated_items_sold_per_month" class="checkout-page-label">Estimated Items Sold Per Month</label>
                        <input type="number" name="estimated_items_sold_per_month" id="estimated_items_sold_per_month" class="checkout-page-input" value="{{ old('estimated_items_sold_per_month') }}" min="0">
                    </div>
                @endif
                
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
                {{--<div class="checkout-page-form-group">
                    <label for="store_email" class="checkout-page-label">Store Email</label>
                    <input type="email" name="store_email" id="store_email" class="checkout-page-input" value="{{ $store->store_email ?? '' }}" required>
                </div>--}}
                <div class="checkout-page-form-group">
                    <label for="store_fb_link" class="checkout-page-label">Store Facebook Link</label>
                    <input type="url" name="store_fb_link" id="store_fb_link" class="checkout-page-input" value="{{ old('store_fb_link') }}">
                </div>
                
                <div class="checkout-page-form-group">
                    <input type="checkbox" id="terms" class="checkout-page-checkbox">
                    <label for="terms" class="checkout-page-label">I have read and agree to the <a href="#terms-conditions" target="_blank">terms and conditions</a>.</label>
                </div>

                <button type="submit" id="submit-button" class="checkout-page-btn" disabled>Proceed to Place Order</button>
            </form>

            <!-- Modal for Terms and Conditions -->
            <div id="terms-modal" class="modal">
                <div class="modal-content">
                    <span class="close-button" onclick="closeModal()">&times;</span>
                    <h2>Terms and Conditions</h2>
                    <ul>
                        <li><strong>Approval:</strong> All orders must be approved by admins.</li>
                        <li><strong>Payment:</strong> Payment must be made on time; late payments will result in item retrieval.</li>
                        <li><strong>Responsibility:</strong> Users are responsible for consigned items; Ukay Supplier Consign is not liable for losses.</li>
                        <li><strong>Returns:</strong> Unsold items must be returned at the user's expense.</li>
                        <li><strong>Compliance:</strong> Items must meet quality and legal standards.</li>
                        <li><strong>Acceptance:</strong> Using the service means accepting these terms.</li>
                    </ul>
                </div>
            </div>
                      


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
        <script src="{{ asset('js/checkout.js?v=5.4') }}"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/checkout.css?v=5.4') }}">
@endsection
