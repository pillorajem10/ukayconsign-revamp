@extends('layout') <!-- Extending the main layout file -->

@section('title', 'Billing Breakdown')

@section('content')
    <div class="billing-breakdown-container">
        <h1 class="billing-breakdown-title">Billing Breakdown for Billing ID: {{ $billing->id }}</h1>

        <div class="back-to-dashboard">
            <a href="{{ route('billings.index') }}" class="btn btn-secondary">Back To Billings</a>
        </div>
        
        <div class="billing-info">
            <p><strong>Issued On:</strong> {{ $billing->bill_issued }}</p>
            <p><strong>Sale Date Range:</strong> {{ $billing->sales_date_range }}</p>
            <p><strong>Status:</strong> {{ $billing->status }}</p>
            <p><strong>Total Bill:</strong> ₱{{ number_format($billing->total_bill, 2) }}</p>
        </div>

        <h3 class="breakdown-title">Breakdown:</h3>
        @if ($billingBreakdown && count($billingBreakdown) > 0)
            <div class="table-container">
                <table class="billing-breakdown-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($billingBreakdown as $item)
                            <tr>
                                <td>{{ $item['product_bundle_id'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>₱{{ number_format($item['sub_total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="no-records">No items found in the billing breakdown.</p>
        @endif
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/billingBreakdown.css?v=7.8') }}">
@endsection
