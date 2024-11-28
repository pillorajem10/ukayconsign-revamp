@extends('layout')

@section('title', 'Sale Breakdown')

@section('content')
    <div class="sale-breakdown-container">
        <h1 class="sale-breakdown-title">Sale Breakdown</h1>

        <div class="mb-3">
            <a href="{{ url()->previous() == route('dashboard') ? route('dashboard') : (url()->previous() == route('tallies.index') ? route('tallies.index', ['store_id' => request('store_id')]) : url()->previous()) }}" class="btn btn-secondary back-btn">
                Back 
            </a>
        </div>
        
        @if(count($breakdown) > 0)
            <div class="table-responsive breakdown-table-container">
                <table class="table table-bordered table-striped breakdown-table">
                    <thead class="thead-dark">
                        <tr>
                            <th>Product Bundle</th>
                            <th>Total Sale (₱)</th>
                            <th>Consign Total (₱)</th>
                            <th>Total Profit (₱)</th>  <!-- New column for total_profit -->
                            <th>Total Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($breakdown as $productBundleId => $data)
                            <tr>
                                <td>{{ $productBundleId }}</td>
                                <td>₱{{ number_format($data['total'], 2) }}</td>
                                <td>₱{{ number_format($data['consign_total'], 2) }}</td>
                                <td>₱{{ number_format($data['total_profit'], 2) }}</td>  <!-- Display total_profit -->
                                <td>{{ $data['quantity'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="no-breakdown-message">No breakdown data available for this store.</p>
        @endif
    
        <div class="chart-parent-cont">
            <div class="chart-container sales-pie-chart-container">
                <!-- Label for Total Sale chart -->
                <div class="chart-label">Total Sale</div>
                <canvas id="salesPieChart" class="sales-pie-chart"></canvas>
            </div>
        
            <div class="chart-container profit-pie-chart-container">
                <!-- Label for Total Profit chart -->
                <div class="chart-label">Total Profit</div>
                <canvas id="profitPieChart" class="profit-pie-chart"></canvas>
            </div>
        </div>        

        <!-- Pass breakdown data to JS using a data attribute -->
        <div id="breakdownData" class="breakdown-data" data-breakdown="{{ json_encode($breakdown) }}" style="display: none;"></div>

        <script src="{{ asset('js/sale-breakdown.js?v=7.8') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/sale-breakdown.css?v=7.8') }}">
@endsection
