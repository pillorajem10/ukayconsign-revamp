@extends('layout')

@section('title', 'Sales Records')

@section('content')
    <div class="sales-container">
        <h2 class="page-title">Sales Records</h2>
        
        @foreach($sales as $sale)
            <div class="sale-card">
                <h3 class="card-title">Sale ID: {{ $sale->id }}</h3>
                <div class="card-content">
                    <p><strong>Total:</strong> {{ number_format($sale->total, 2) }}</p>
                    <p><strong>Amount Paid:</strong> {{ number_format($sale->amount_paid, 2) }}</p>
                    <p><strong>Created At:</strong> {{ $sale->createdAt->format('Y-m-d H:i') }}</p>
                </div>

                <h4 class="items-title">Ordered Items</h4>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product SKU</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(json_decode($sale->ordered_items) as $item)
                            <tr>
                                <td>{{ $item->product_sku }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price, 2) }}</td>
                                <td>{{ number_format($item->sub_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/saleDetails.css?v=1.6') }}">
@endsection
