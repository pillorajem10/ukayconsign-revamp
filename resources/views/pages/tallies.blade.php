@extends('layout')

@section('title', 'Tallies')

@section('content')
    <div class="tallies-container">
        <p class="welcome-message">Earnings of {{ $store->store_name }}</p>

        <div class="back-to-dashboard">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        
        <form method="GET" action="{{ route('tallies.index') }}" class="mb-3">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <label for="filter" class="mr-2">Filter by:</label>
                    <select name="filter" id="filter" class="form-control" onchange="this.form.submit()">
                        <option value="daily" {{ $filter == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="monthly" {{ $filter == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ $filter == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
                <div class="col-auto">
                    <input type="hidden" name="store_id" value="{{ request()->get('store_id') }}">
                </div>
            </div>
        </form>

        @if($tallies->isEmpty())
            <p class="no-tallies-message">No tallies available for your stores.</p>
        @else
            <div class="table-responsive">
                <table class="tallies-table">
                    <thead class="table-header">
                        <tr>
                            @if($filter == 'daily')
                                <th class="header-cell">Date</th>
                            @elseif($filter == 'monthly')
                                <th class="header-cell">Month</th>
                            @elseif($filter == 'yearly')
                                <th class="header-cell">Year</th>
                            @endif
                            <th class="header-cell">Total</th>
                            <th class="header-cell">Action</th> <!-- New column for the button -->
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        @foreach ($tallies as $tally)
                            <tr class="table-row">
                                @if($filter == 'daily')
                                <td class="data-cell" data-label="Date">{{ \Carbon\Carbon::parse($tally->createdAt)->format('M. d, Y') }}</td>
                                @elseif($filter == 'monthly')
                                <td class="data-cell" data-label="Month">{{ \Carbon\Carbon::createFromFormat('m', $tally->month)->format('M.') }} {{ $tally->year }}</td>
                                @elseif($filter == 'yearly')
                                    <td class="data-cell" data-label="Year">{{ $tally->year }}</td>
                                @endif
                                <td class="data-cell" data-label="Total">₱{{ number_format($tally->total, 2) }}</td>

                                <td class="data-cell" data-label="Action">
                                    @if($filter == 'daily')
                                        <a href="{{ route('saleBreakdown.index', ['store_id' => $store->id, 'day' => \Carbon\Carbon::parse($tally->createdAt)->toDateString(), 'filter' => 'daily']) }}" class="btn btn-info">
                                            View Breakdown
                                        </a>                                    
                                    @elseif($filter == 'monthly')
                                        <a href="{{ route('saleBreakdown.index', ['store_id' => $store->id, 'month' => $tally->month, 'filter' => 'monthly']) }}" class="btn btn-info">
                                            View Breakdown
                                        </a>
                                    @elseif($filter == 'yearly')
                                        <a href="{{ route('saleBreakdown.index', ['store_id' => $store->id, 'year' => $tally->year, 'filter' => 'yearly']) }}" class="btn btn-info">
                                            View Breakdown
                                        </a>
                                    @endif
                                </td>                                                                                                                           
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <script src="{{ asset('js/tallies.js?v=7.5') }}"></script>
    </div>

    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            {{ $tallies->appends(['filter' => $filter, 'store_id' => request()->get('store_id')])->links('vendor.pagination.bootstrap-4') }}
        </ul>
    </nav>

@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/tallies.css?v=7.5') }}">
@endsection
