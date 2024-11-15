@extends('layout')

@section('title', 'Sales Records')

@section('content')
    <div class="sales-container">
        <h2 class="page-title">Sales Records</h2>

        <div class="back-to-dashboard">
            <a href="{{ route('stores.index') }}" class="btn btn-secondary">Back to Stores</a>
        </div>
        
        <!-- Filter Form -->
        <div class="filter-form-container">
            <form action="{{ url('sales') }}" method="GET" class="filter-form">
                <input type="hidden" name="store_id" value="{{ $storeId }}">
        
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" class="form-input" value="{{ request('start_date') }}">
                </div>
        
                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" id="end_date" class="form-input" value="{{ request('end_date') }}">
                </div>
        
                <button type="submit" class="filter-button">Filter</button>
            </form>
        </div>        

        @if($sales->isEmpty())
            <p class="no-sales-message">No Sale Found</p>
        @else
            @foreach($sales as $sale)
                <div class="sale-card">
                    <h3 class="card-title">Sale ID: {{ $sale->id }}</h3>
                    <div class="card-content">
                        <p><strong>Customer Name:</strong> {{ $sale->customer_name }}</p>
                        <p><strong>Customer Number:</strong> {{ $sale->customer_number }}</p>
                        <p><strong>Total:</strong> {{ number_format($sale->total, 2) }}</p>

                        <!-- Payment Method Display -->
                        <p><strong>Mode of Payment:</strong> {{ $sale->mode_of_payment }}</p>

                        <!-- Conditional Fields -->
                        <div id="cashFields" style="{{ $sale->mode_of_payment === 'Cash' ? '' : 'display: none;' }}">
                            <p><strong>Amount Paid:</strong> {{ number_format($sale->amount_paid, 2) }}</p>
                            <p><strong>Change Given:</strong> {{ number_format($sale->cx_change, 2) }}</p>
                        </div>

                        <div id="ewalletFields" style="{{ $sale->mode_of_payment === 'eWallet' ? '' : 'display: none;' }}">
                            <p><strong>Reference Number (eWallet):</strong> {{ $sale->ref_number_ewallet }}</p>
                        </div>

                        <p><strong>Date of Transaction:</strong> {{ \Carbon\Carbon::parse($sale->date_of_transaction)->format('F j, Y') }}</p>
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
                                    <td>{{ $item->product_bundle_id }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price, 2) }}</td>
                                    <td>{{ number_format($item->sub_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif

        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                {{ $sales->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
            </ul>
        </nav>  
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/saleDetails.css?v=7.2') }}">
@endsection
