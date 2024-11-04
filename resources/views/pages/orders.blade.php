@extends('layout')

@section('title', 'Orders')

@section('content')
    <div>
        <h1 class="text-center mb-4">Orders</h1>

        <div class="row">
            @forelse ($orders as $order)
                <div class="col-12 mb-3">
                    <div class="card order-card shadow-lg rounded w-100">
                        <div class="card-body">
                            <h5 class="card-title">Order ID: {{ $order->id }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Customer: {{ $order->first_name }} {{ $order->last_name }}</h6>
                            <h6 class="card-subtitle mb-2 text-muted">Store Name: {{ $order->store_name }}</h6>
                            <p><strong>Email:</strong> {{ $order->email }}</p>
                            <p><strong>Address:</strong> {{ $order->address }}</p>

                            <!-- Order Tracker or Status Message -->
                            @if ($order->order_status == 'Canceled')
                                <p class="text-danger"><strong>Status:</strong> Canceled</p>
                            @else
                                <div class="order-tracker">
                                    <div class="tracker-status">
                                        <div class="status {{ $order->order_status == 'Processing' ? 'active' : '' }}">Processing</div>
                                        <div class="status {{ $order->order_status == 'Packed' ? 'active' : '' }}">Packed</div>
                                        <div class="status {{ $order->order_status == 'Shipped' ? 'active' : '' }}">Shipped</div>
                                        <div class="status {{ $order->order_status == 'Delivered' ? 'active' : '' }}">Delivered</div>
                                    </div>
                                    <div class="tracker-line">
                                        <div class="line {{ in_array($order->order_status, ['Processing', 'Packed', 'Shipped', 'Delivered']) ? 'active' : '' }}"></div>
                                        <div class="line {{ in_array($order->order_status, ['Packed', 'Shipped', 'Delivered']) ? 'active' : '' }}"></div>
                                        <div class="line {{ in_array($order->order_status, ['Shipped', 'Delivered']) ? 'active' : '' }}"></div>
                                        <div class="line {{ $order->order_status == 'Delivered' ? 'active' : '' }}"></div>
                                    </div>                                
                                </div>
                            @endif

                            <h6 class="mt-3"><strong>Products Ordered:</strong></h6>
                            <table class="table table-sm mt-2">
                                <thead>
                                    <tr>
                                        <th>Bundle Name</th>
                                        <th>Category</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $products = json_decode($order->products_ordered, true);
                                    @endphp
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product['bundle_name'] }}</td>
                                            <td>{{ $product['category'] }}</td>
                                            <td>{{ $product['quantity'] }}</td>
                                            <td>₱{{ number_format($product['price'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            <p class="mt-3"><strong>Total Price:</strong> ₱{{ number_format($order->total_price, 2) }}</p>
                            <p><strong>Order Date:</strong> {{ $order->order_date }}</p>
                            
                            <div class="order-page-action-btn">
                                @if (in_array($order->order_status, ['Processing']))
                                    <div>
                                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="order_status" value="Canceled">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel Order</button>
                                        </form>
                                    </div>
                                @else
                                    <div><button class="btn btn-danger" disabled>Cancel Order</button></div>
                                @endif
                                @if (!in_array($order->order_status, ['Canceled', 'Delivered']))
                                    <div>
                                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="order_status" value="Delivered">
                                            <button type="submit" class="btn btn-info" onclick="return confirm('Are you sure you already received the order?')">Received Order</button>
                                        </form>
                                    </div>
                                @else
                                    <div><button class="btn btn-info" disabled>Received Order</button></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <div class="alert alert-warning">No orders found.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/ordersPage.css?v=5.4') }}">
@endsection
