@extends('layout') <!-- Extending the main layout file -->

@section('title', 'Billing') <!-- Setting the title for this page -->

@section('content')
    <div class="billing-container">
        <h1 class="billing-title">Billing Records</h1>

        <div class="back-to-dashboard">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back To Dashboard</a>
        </div>

        <!-- Check if there are any billings -->
        @if($billings->isEmpty())
            <p class="no-records">No billing records found.</p>
        @else
            <div class="table-container">
                <table class="billing-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Total Bill</th>
                            <th>Issued On</th>
                            <th>Action</th> <!-- Added a column for the action -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($billings as $billing)
                            <tr>
                                <td>{{ $billing->id }}</td>
                                <td>{{ $billing->status }}</td>
                                <td>₱{{ number_format($billing->total_bill, 2) }}</td>
                                <td>{{ $billing->bill_issued }}</td>
                                <td>
                                    <!-- "View Breakdown" button which links to the 'show' route -->
                                    <a href="{{ route('billings.show', $billing->id) }}" class="btn btn-primary">View Breakdown</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/billing.css?v=6.6') }}">
@endsection
