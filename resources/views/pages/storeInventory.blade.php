@extends('layout')

@section('title', 'Stores Inventory')

@section('content')
    <div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product ID</th>
                        <th>Stocks</th>
                        <th>Consign</th>
                        <th>SRP</th>
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <link rel="stylesheet" href="{{ asset('css/storeInv.css?v=3.2') }}">
    </div>
@endsection
