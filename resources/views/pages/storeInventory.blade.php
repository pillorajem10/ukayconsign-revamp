@extends('layout')

@section('title', 'Stores Inventory')

@section('content')
<div>
    <h1>Store Inventory List</h1>

    <table class="table">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Product ID</th>
                <th>Stocks</th>
                <th>Consign</th>
                <th>SPR</th>
                <th>Store ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventory as $item)
            <tr>
                <td>{{ $item->SKU }}</td>
                <td>{{ $item->ProductID }}</td>
                <td>{{ $item->Stocks }}</td>
                <td>{{ $item->Consign }}</td>
                <td>{{ $item->SPR }}</td>
                <td>{{ $item->store_id }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination Links -->
    {{ $inventory->links() }}
</div>
@endsection

