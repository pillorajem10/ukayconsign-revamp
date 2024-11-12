@extends('layout') <!-- Extending the main layout file -->

@section('title', 'Billing Breakdown')

@section('content')
    <h1>Billing Breakdown for Billing ID: {{ $billing->id }}</h1>
    
    <p><strong>Issued On:</strong> {{ $billing->bill_issued }}</p>
    <p><strong>Sale Date Range:</strong> {{ $billing->sales_date_range }}</p>
    <p><strong>Status:</strong> {{ $billing->status }}</p>
    <p><strong>Total Bill:</strong> ₱{{ number_format($billing->total_bill, 2) }}</p>

    <h3>Breakdown:</h3>
    @if ($billingBreakdown && count($billingBreakdown) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Product SKU</th>
                    <th>Quantity</th>
                    <th>Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($billingBreakdown as $item)
                    <tr>
                        <td>{{ $item['product_sku'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>₱{{ number_format($item['sub_total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No items found in the billing breakdown.</p>
    @endif
@endsection
