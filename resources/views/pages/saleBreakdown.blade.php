@extends('layout')

@section('title', 'Sale Breakdown')

@section('content')
    <div class="sale-breakdown-container">
        <h1>Sale Breakdown</h1>

        <div class="mb-3">
            <a href="{{ route('tallies.index', ['store_id' => request('store_id')]) }}" class="btn btn-secondary">Back To Earnings</a>
        </div>

        <!-- Display Sale Breakdown Table -->
        @if(count($breakdown) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Product Bundle</th>
                            <th>Total Sale (₱)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($breakdown as $productBundleId => $totalSubTotal)
                            <tr>
                                <td>{{ $productBundleId }}</td>
                                <td>₱{{ number_format($totalSubTotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>No breakdown data available for this store.</p>
        @endif

        <!-- Chart.js Pie Chart -->
        <div class="chart-container">
            <canvas id="salesPieChart"></canvas>
        </div>

        <!-- Pass breakdown data to JS using a data attribute -->
        <div id="breakdownData" data-breakdown="{{ json_encode($breakdown) }}" style="display: none;"></div>

        <script src="{{ asset('js/sale-breakdown.js?v=6.6') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/sale-breakdown.css?v=6.6') }}">
@endsection

