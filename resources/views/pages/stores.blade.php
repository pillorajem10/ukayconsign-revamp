@extends('layout')

@section('title', 'Stores')

@section('content')
<div>
    <h1>Store List</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Owner</th>
                <th>Address</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Total Earnings</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stores as $store)
            <tr>
                <td>{{ $store->id }}</td>
                <td>{{ $store->store_name }}</td>
                <td>{{ $store->store_owner }}</td>
                <td>{{ $store->store_address }}</td>
                <td>{{ $store->store_phone_number }}</td>
                <td>{{ $store->store_email }}</td>
                <td>{{ $store->store_total_earnings }}</td>
                <td>{{ $store->store_status }}</td>
                <td>
                    <a href="{{ url('/store-inventory?store_id=' . $store->id) }}" class="btn btn-info">View Inventory</a>
                </td>
            </tr>
            @endforeach
        </tbody>              
    </table>
</div>
@endsection
