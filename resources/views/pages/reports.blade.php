@extends('layout')

@section('title', 'Reports')

@section('content')
    <div>
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
        <div class="all-charts-container">
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
    
            <!-- Canvas for the ordered items chart -->
            <div class="chart-container">
                <canvas id="orderedItemsChart"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Pass the monthly sales data and ordered items sales data to JavaScript
            const monthlySales = @json($monthlySales);
            const orderedItemsSales = @json($orderedItemsSales);
        </script>
        <script src="{{ asset('js/reports.js?v=5.2') }}"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/reports.css?v=5.2') }}">
@endsection
