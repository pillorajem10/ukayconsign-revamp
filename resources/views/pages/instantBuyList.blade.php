@extends('layout')

@section('title', 'Instant Buy List')

@section('content')
    <div class="instant-buy-container">
        <h1 class="instant-buy-title">Instant Buy Products</h1>
        <div class="table-responsive"> <!-- Added a wrapper for scrolling -->
            <table class="instant-buy-table">
                <thead>
                    <tr>
                        <th class="table-header">Product Barcode</th>
                        <th class="table-header">Name</th>
                        <th class="table-header">Description</th>
                        <th class="table-header">Price</th>
                        <th class="table-header">Images</th>
                        {{--<th class="table-header">Actions</th>--}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr class="table-row">
                            <td class="table-cell">{{ $product->product_barcode }}</td>
                            <td class="table-cell">{{ $product->name }}</td>
                            <td class="table-cell">{{ $product->description }}</td>
                            <td class="table-cell">₱{{ number_format($product->price, 2) }}</td>
                            <td class="table-cell">
                                @if ($product->images)
                                    @php
                                        $images = json_decode($product->images);
                                    @endphp
                                    @foreach ($images as $base64)
                                        <img src="data:image/jpeg;base64,{{ $base64 }}" alt="Image" class="product-image">
                                    @endforeach
                                @else
                                    No images
                                @endif
                            </td>
                            {{--<td class="table-cell">
                                <!-- Add action buttons here (edit, delete, etc.) -->
                            </td>--}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/instantBuyList.css?v=5.2') }}">
@endsection
