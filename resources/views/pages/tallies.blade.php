@php
    use Carbon\Carbon;
@endphp

@extends('layout')

@section('title', 'Tallies')

@section('content')
    <div class="tallies-container">
        <p class="welcome-message">Tallies</p>

        <form class="tally-filter-form" method="GET" action="{{ route('tallies.index') }}">
            <div class="form-group">
                <label class="date-label" for="start_date">Start Date:</label>
                <input class="date-input" type="date" name="start_date" id="start_date" value="{{ request('start_date') }}">
            </div>
        
            <div class="form-group">
                <label class="date-label" for="end_date">End Date:</label>
                <input class="date-input" type="date" name="end_date" id="end_date" value="{{ request('end_date') }}">
            </div>
        
            <div class="form-group">
                <label class="store-label" for="store_id">Select Store:</label>
                <select class="store-select" name="store_id" id="store_id">
                    <option value="">All Stores</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                            {{ $store->store_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        
            <div class="button-group">
                <button class="filter-button" type="submit">Filter</button>
                <a class="clear-button" href="{{ route('tallies.index') }}">Clear</a>
            </div>
        </form>              
        
        @if($tallies->isEmpty())
            <p class="no-tallies-message">No tallies available for your stores.</p>
        @else
            @if(request('start_date') && request('end_date'))
                <p class="showing-tallies-message">
                    Showing tallies from 
                    <span class="highlight-date">{{ Carbon::parse(request('start_date'))->format('F j, Y') }}</span> 
                    to 
                    <span class="highlight-date">{{ Carbon::parse(request('end_date'))->format('F j, Y') }}</span>.
                </p>
            @endif
    
            <table class="tallies-table">
                <thead class="table-header">
                    <tr>
                        <th class="header-cell">Store</th>
                        <th class="header-cell">Date</th>
                        <th class="header-cell">Total</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    @foreach ($tallies as $tally)
                        <tr class="table-row">
                            <td class="data-cell" data-label="Store Name">{{ $tally->store->store_name }}</td> <!-- Use store_name -->
                            <td class="data-cell" data-label="Date">{{ Carbon::parse($tally->createdAt)->format('F j, Y') }}</td>
                            <td class="data-cell" data-label="Total">₱{{ number_format($tally->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>                
            </table>
        @endif
        <script src="{{ asset('js/tallies.js?v=2.5') }}"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/tallies.css?v=2.5') }}">
@endsection
