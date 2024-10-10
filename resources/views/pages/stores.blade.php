@extends('layout')

@section('title', 'Stores')

@section('content')
<div>
    <h1 class="store-list-title">Store List</h1>
    <div class="store-table-responsive">
        <table class="store-table">
            <thead class="store-table-header">
                <tr>
                    <th class="store-table-cell">Name</th>
                    <th class="store-table-cell">Total Earnings</th>
                    <th class="store-table-cell">Status</th>
                    <th class="store-table-cell">Action</th>
                </tr>
            </thead>
            <tbody class="store-table-body">
                @foreach($stores as $store)
                <tr class="store-table-row">
                    <td class="store-table-cell">{{ $store->store_name }}</td>
                    <td class="store-table-cell">{{ $store->store_total_earnings }}</td>
                    <td class="store-table-cell">{{ $store->store_status }}</td>
                    <td class="store-table-cell">
                        <a href="{{ url('/store-inventory?store_id=' . $store->id) }}" class="btn btn-info">View Inventory</a>
                        <a href="{{ url('/sales?store_id=' . $store->id) }}" class="btn btn-primary">View Sales</a>
                    </td>
                </tr>
                @endforeach
            </tbody>              
        </table>
    </div>
</div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/stores.css?v=3.1') }}">
@endsection
