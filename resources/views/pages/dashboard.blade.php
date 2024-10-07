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
                <p class="welcome-message">Earnings</p>
                <table class="earnings-table">
                    <thead class="table-header">
                        <tr>
                            <th class="header-cell">Store Name</th>
                            <th class="header-cell">Total Today</th>
                            <th class="header-cell">Total This Month</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        @foreach ($storeEarnings as $storeName => $earnings)
                            <tr class="table-row">
                                <td class="data-cell" data-label="Store Name">{{ $storeName }}</td>
                                <td class="data-cell" data-label="Total Today">₱{{ number_format($earnings['total_today'], 2) }}</td>
                                <td class="data-cell" data-label="Total This Month">₱{{ number_format($earnings['total_month'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>                       

            <div class="dashboard-container">
                <h2 class="welcome-message">Your Stores</h2>
                @if($stores->isEmpty())
                    <p>No stores found.</p>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Total Earnings (Overall)</th>
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
                <h2 class="welcome-message">Top 5 Most Sold Products (All stores combined)</h2>
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
                <h2 class="welcome-message">Your Orders</h2>
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

        <script src="{{ asset('js/dashboard.js?v=2.4') }}"></script>
    </div>
@endsection


@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css?v=2.4') }}">
@endsection