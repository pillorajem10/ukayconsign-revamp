@extends('layout')

@section('title', 'Reports')

@section('content')
    <div>
        <h1>Sales Report</h1>

        <!-- Store selection form -->
        <form id="storeForm" method="GET" class="mb-4 form-inline">
            <div class="form-group">
                <label for="storeSelect" class="form-label">Select Store:</label>
                <select name="store_id" id="storeSelect" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ request('store_id') == 'all' ? 'selected' : '' }}>All Stores</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>{{ $store->store_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="monthSelect" class="form-label">Select Month:</label>
                <select name="month" id="monthSelect" class="form-select" onchange="this.form.submit()">
                    <option value="">All Months</option>
                    @for ($month = 1; $month <= 12; $month++)
                        <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
        </form>

        <!-- Canvas for the sales chart -->
        <div class="all-charts-container ">
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
    
            <!-- Canvas for the ordered items chart -->
            <div class="chart-container">
                <canvas id="orderedItemsChart"></canvas>
            </div>
    
            <!-- Canvas for the quantity per product bundle chart -->
            <div class="chart-container">
                <canvas id="quantityChart"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Pass the monthly sales data, ordered items sales data, and quantities data to JavaScript
            const monthlySales = @json($monthlySales);
            const orderedItemsSales = @json($orderedItemsSales);
            const quantityPerBundle = @json($quantityPerBundle);
        </script>
        <script src="{{ asset('js/reports.js?v=5.4') }}"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/reports.css?v=5.4') }}">
@endsection
