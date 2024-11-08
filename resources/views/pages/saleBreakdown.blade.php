@extends('layout')

@section('title', 'Sale Breakdown')

@section('content')
    <div class="sale-breakdown-container">
        <h1>Sale Breakdown</h1>

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
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/sale-breakdown.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('js/sale-breakdown.js') }}"></script>
@endsection
