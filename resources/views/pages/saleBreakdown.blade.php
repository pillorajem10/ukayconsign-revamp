@extends('layout')

@section('title', 'Sale Breakdown')

@section('content')
    <div class="sale-breakdown-container">
        <h1 class="sale-breakdown-title">Sale Breakdown</h1>

        <div class="mb-3">
            <a href="{{ url()->previous() == route('dashboard') 
                ? route('dashboard') 
                : (url()->previous() == route('tallies.index') 
                    ? route('tallies.index', ['store_id' => request('store_id')]) 
                    : url()->previous()) }}" 
                class="btn btn-secondary back-btn">
                Back
            </a>
            <!-- New route for View Sale By Customers -->
            <a href="{{ route('sales.index', ['store_id' => request('store_id')]) }}" class="btn btn-dark-green ml-2">
                View Sale By Customers
            </a>            
        </div>
        
        
        @if(count($breakdown) > 0)
            <div class="table-responsive breakdown-table-container">
                <table class="table table-bordered table-striped breakdown-table">
                    <thead class="thead-dark">
                        <tr>
                            <th>Product</th>
                            <th>QTY</th>
                            <th>Sale</th>
                            <th>Consign</th>
                            <th>Net</th>  <!-- New column for total_profit -->
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQuantity = 0;
                            $totalSale = 0;
                            $totalConsign = 0;
                            $totalProfit = 0;
                        @endphp
                
                        @foreach ($breakdown as $productBundleId => $data)
                            <tr>
                                <td>{{ $productBundleId }}</td>
                                <td>{{ $data['quantity'] }}</td>
                                <td>₱{{ number_format($data['total'], 2) }}</td>
                                <td>₱{{ number_format($data['consign_total'], 2) }}</td>
                                <td>₱{{ number_format($data['total_profit'], 2) }}</td>
                
                                @php
                                    // Accumulate totals for the sums
                                    $totalQuantity += $data['quantity'];
                                    $totalSale += $data['total'];
                                    $totalConsign += $data['consign_total'];
                                    $totalProfit += $data['total_profit'];
                                @endphp
                            </tr>
                        @endforeach
                
                        <!-- Blank Row Before Total -->
                        <tr>
                            <td colspan="5"></td> <!-- This will create a blank row, spanning all columns -->
                        </tr>
                    </tbody>
                
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <td><strong>{{ $totalQuantity }}</strong></td>
                            <td><strong>₱{{ number_format($totalSale, 2) }}</strong></td>
                            <td><strong>₱{{ number_format($totalConsign, 2) }}</strong></td>
                            <td><strong>₱{{ number_format($totalProfit, 2) }}</strong></td>
                        </tr>
                    </tfoot>
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

        <script src="{{ asset('js/sale-breakdown.js?v=8.0') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/sale-breakdown.css?v=8.0') }}">
@endsection
