@extends('layout')

@section('title', 'Customers Information')

@section('content')
    <div class="customer-info-container">
        <h1 class="page-title">Customer Information</h1>

        <!-- Display success and error messages -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filter Form for Interest -->
        <div class="filter-container">
            <form action="{{ route('cxInfos.index') }}" method="GET" class="filter-form" id="filterForm">
                <!-- Hidden store_id field to preserve the store_id in the URL -->
                <input type="hidden" name="store_id" value="{{ request('store_id') ?? $store->id }}">

                <div class="form-group">
                    <label for="interest_filter" class="form-label">Filter by Interest:</label>
                    <select name="interest_filter" id="interest_filter" class="form-select" onchange="this.form.submit()">
                        <option value="">Select Interest</option>
                        <option value="Shoes" {{ request('interest_filter') == 'Shoes' ? 'selected' : '' }}>Shoes</option>
                        <option value="Shirts" {{ request('interest_filter') == 'Shirts' ? 'selected' : '' }}>Shirts</option>
                        <option value="Caps" {{ request('interest_filter') == 'Caps' ? 'selected' : '' }}>Caps</option>
                        <option value="Bags" {{ request('interest_filter') == 'Bags' ? 'selected' : '' }}>Bags</option>
                        <option value="Hoodies" {{ request('interest_filter') == 'Hoodies' ? 'selected' : '' }}>Hoodies</option>
                        <option value="Shorts" {{ request('interest_filter') == 'Shorts' ? 'selected' : '' }}>Shorts</option>
                        <option value="Pants" {{ request('interest_filter') == 'Pants' ? 'selected' : '' }}>Pants</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="back-to-dashboard">
            <a href="{{ route('stores.index') }}" class="btn btn-secondary">Back to Stores</a>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary" id="openEmailBlastModal">Write Email Blast</button>
        </div>

        <!-- Check if there are any customer records -->
        @if($cxInfos->isEmpty())
            <p class="no-data-message">No customer information available.</p>
        @else
            <div class="table-container">
                <table class="cx-info-table">
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
            </div>
        @endif

        <!-- Modal for Email Blast -->
        <div id="emailBlastModal" class="modal-overlay">
            <div class="modal-content">
                <h3>Email Blast</h3>
                <form id="emailBlastForm" action="{{ route('sendBlastEmails') }}" method="POST">
                    @csrf
                    <!-- Hidden store_id field -->
                    <input type="hidden" name="store_id" value="{{ request('store_id') ?? $store->id }}">
        
                    <!-- Hidden interest_filter field -->
                    <input type="hidden" name="interest_filter" value="{{ request('interest_filter') ?? '' }}">
        
                    <div class="form-group">
                        <label for="store_name" class="form-label">Store Name (From):</label>
                        <input type="text" name="store_name" id="store_name" class="form-control" value="{{ $store->store_name }}" readonly>
                    </div>
        
                    <div class="form-group">
                        <label for="subject" class="form-label">Subject:</label>
                        <input type="text" name="subject" id="subject" class="form-control" required>
                    </div>
        
                    <div class="form-group">
                        <label for="body" class="form-label">Body:</label>
                        <textarea name="body" id="body" class="form-control" rows="5" required></textarea>
                    </div>
        
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success">Send Email Blast</button>
                        <button type="button" class="btn btn-secondary" id="closeEmailBlastModal">Close</button>
                    </div>
                </form>
            </div>
        </div>
              
        
        <script src="{{ asset('js/cxInfos.js?v=8.0') }}"></script>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/cxInfos.css?v=8.0') }}">
@endsection
