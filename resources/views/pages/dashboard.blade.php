@extends('layout')

@section('title', 'Dashboard')

<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner" id="loadingSpinner"></div>
</div>

@section('content')
    <div class="dashboard-page">
        <div class="promo-container">
            <h2>Check out our promos for you!</h2>
            @if($promos->isEmpty())
                <p>No promos available.</p>
            @else
                <div class="promo-images">
                    @foreach($promos as $promo)
                        @php
                            $imageData = base64_encode($promo->image);
                            $src = 'data:image/jpeg;base64,' . $imageData;
                        @endphp
                        <img src="{{ $src }}" alt="Promo Image" style="max-width: 100%; border-radius: 8px; margin: 10px 0;">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="small-section-container">
            <div class="dashboard-container">
                <p class="welcome-message">Welcome to your dashboard!</p>

                <div class="user-details">
                    <h2>User Details</h2>
                    <ul>
                        <li><strong>Email:</strong> {{ $user->email }}</li>
                    </ul>
                </div>
            </div>

            <div class="dashboard-container">
                <h2>Your Stores</h2>
                @if($stores->isEmpty())
                    <p>No stores found.</p>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Total Earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stores as $store)
                                <tr>
                                    <td>{{ $store->store_name }}</td>
                                    <td>₱{{ number_format($store->store_total_earnings, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                <div class="text-center mt-3">
                    <a href="/stores" class="btn btn-success">Manage Stores</a>
                </div>
            </div>
            
            <div class="dashboard-container">
                <h2>Top 5 Most Sold Products (All stores combine)</h2>
                @if(empty($mostSoldProducts))
                    <p>No sales data available.</p>
                @else
                    <ul>
                        @foreach($mostSoldProducts as $sku => $data)
                            <li>
                                <strong>Product Bundle ID:</strong> {{ $data['product_bundle_id'] }} | 
                                <strong>Sold Count:</strong> {{ $data['count'] }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            
            <div class="dashboard-container">
                <h2>Your Orders</h2>
                @if($orders->isEmpty())
                    <p>No orders found.</p>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->order_status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                <div class="text-center mt-3">
                    <a href="/orders" class="btn btn-success">Check Orders</a>
                </div>
            </div>            
        </div>

        <script src="{{ asset('js/dashboard.js?v=2.3') }}"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css?v=2.3') }}">
@endsection
