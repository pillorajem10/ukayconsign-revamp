@extends('layout') <!-- Extending the main layout file -->

@section('title', 'Billing') <!-- Setting the title for this page -->

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="billing-container">
        <h1 class="billing-title">Billing Records</h1>

        <div class="back-to-dashboard mb">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back To Dashboard</a>
        </div>

        @if($billings->isEmpty())
            <p class="no-records">No billing records found.</p>
        @else
            <div class="table-container">
                <table class="billing-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Total Bill</th>
                            <th>Status</th>
                            <th>Issued On</th>
                            <th>Action</th> <!-- Added a column for the action -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($billings as $billing)
                            <tr>
                                <td>{{ $billing->id }}</td>
                                <td>₱{{ number_format($billing->total_bill, 2) }}</td>
                                <td>{{ $billing->status }}</td>
                                <td>{{ \Carbon\Carbon::parse($billing->bill_issued)->format('M. d, Y') }}</td>
                                <td>
                                    <!-- "View Breakdown" button which links to the 'show' route -->
                                    <div class="action-btns">
                                        <a href="{{ route('billings.show', $billing->id) }}" class="action-btn">View Breakdown</a>
                                        <a href="{{ route('billings.showUploadProofOfPayment', $billing->id) }}" class="action-btn">Upload Proof Of Payment</a>
                                    </div>
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
    <link rel="stylesheet" href="{{ asset('css/billing.css?v=6.8') }}">
@endsection
