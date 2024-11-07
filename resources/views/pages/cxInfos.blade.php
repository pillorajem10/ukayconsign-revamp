@extends('layout')

@section('title', 'Customers Information')

@section('content')
    <div class="container">
        <h1>Customer Information</h1>

        <!-- Check if there are any customer records -->
        @if($cxInfos->isEmpty())
            <p>No customer information available.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Customer Type</th>
                        <th>Interest</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loop through all the cxInfos and display them -->
                    @foreach($cxInfos as $index => $cxInfo)
                        <tr>
                            <td>{{ $cxInfo->cx_name ?? 'N/A' }}</td>
                            <td>{{ $cxInfo->email ?? 'N/A' }}</td>
                            <td>{{ $cxInfo->phone_number ?? 'N/A' }}</td>
                            <td>{{ $cxInfo->cx_type ?? 'N/A' }}</td>
                            <td>{{ $cxInfo->interest ?? 'N/A' }}</td>
                            <td>{{ $cxInfo->remarks ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
