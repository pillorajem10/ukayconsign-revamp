@php
    use Carbon\Carbon;
@endphp

@extends('layout')

@section('title', 'Dashboard')

@section('content')
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner" id="loadingSpinner"></div>
    </div>

    <div class="dashboard-page">
        {{--<div class="promo-modal" id="promoModal" style="display: none;">
            <div class="promo-modal-content">
                <span class="close" id="closeModal">&times;</span>
                <h2 class="welcome-message">Check out our promos for you!</h2>
                @if($promos->isEmpty())
                    <p>No promos available.</p>
                @else
                    <div class="promo-images">
                        @foreach($promos as $promo)
                            @php
                                $imageData = base64_encode($promo->image);
                                $src = 'data:image/jpeg;base64,' . $imageData;
                            @endphp
                            <img src="{{ $src }}" alt="Promo Image" class="promo-image">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>--}}      

        <div class="small-section-container">
            <div class="dashboard-container">
                <p class="welcome-message">Welcome to your dashboard!</p>
            
                <div class="user-details">
                    <h2>User Details</h2>
                    <ul>
                        <li><strong>Email:</strong> {{ $user->email }}</li>
                        <li><strong>Badge:</strong></li>
                    </ul>
            
                    @if($user->badge == 'Silver')
                        <img src="{{ asset('images/Silver.png') }}" alt="Silver Badge" style="max-width: 160px; border-radius: 8px; margin: 10px 0;">
                    @elseif($user->badge == 'Gold')
                        <img src="{{ asset('images/Gold.png') }}" alt="Gold Badge" style="max-width: 160px; border-radius: 8px; margin: 10px 0;">
                    @elseif($user->badge == 'Platinum')
                        <img src="{{ asset('images/Plat.png') }}" alt="Platinum Badge" style="max-width: 160px; border-radius: 8px; margin: 10px 0;">
                    @endif
                </div>
            
                <!-- Show Promos Button -->
                <div class="text-center mt-3">
                    <button id="showPromosButton" class="btn btn-info">Check Our Promos</button>
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

        
        <div class="small-section-container">
            <div class="dashboard-container">
                <p class="welcome-message">Earnings</p>
                <table class="earnings-table">
                    <thead class="table-header">
                        <tr>
                            <th class="header-cell">Store</th>
                            <th class="header-cell">Today</th>
                            <th class="header-cell">This Month</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        @foreach ($storeEarnings as $storeId => $earnings)
                            <tr class="table-row">
                                <td class="data-cell" data-label="Store">{{ $earnings['store_name'] }}</td>
                                <td class="data-cell" data-label="Today">₱{{ number_format($earnings['total_today'], 2) }}</td>
                                <td class="data-cell" data-label="This Month">₱{{ number_format($earnings['total_month'], 2) }}</td>
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
                <p class="welcome-message">Tallies Yesterday</p>
                @if($tallies->isEmpty())
                    <p class="no-tallies-message">No tallies available for yesterday.</p>
                @else
                    <table class="earnings-table">
                        <thead class="table-header">
                            <tr>
                                <th class="header-cell">Store</th>
                                <th class="header-cell">Date</th>
                                <th class="header-cell">Total</th>
                            </tr>
                        </thead>
                        <tbody class="table-body">
                            @foreach ($tallies as $tally)
                                <tr class="table-row">
                                    <td class="data-cell" data-label="Store Name">{{ $tally->store->store_name }}</td>
                                    <td class="data-cell" data-label="Date">{{ Carbon::parse($tally->createdAt)->format('F j, Y') }}</td>
                                    <td class="data-cell" data-label="Total">₱{{ number_format($tally->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-center mt-3">
                        <a href="/tallies" class="btn btn-success">View Tallies</a>
                    </div>
                @endif
            </div>           
        </div>

        <script src="{{ asset('js/dashboard.js?v=4.1') }}"></script>
    </div>
@endsection


@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css?v=4.1') }}">
@endsection
