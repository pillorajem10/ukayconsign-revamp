@extends('layout')

@section('title', 'Return Request List')

@section('content')
    <div>
        {{-- Success and Error Messages --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover return-request-table">
                <thead class="table-header">
                    <tr>
                        <th scope="col" class="table-cell">Return ID</th>
                        <th scope="col" class="table-cell">Product</th>
                        <th scope="col" class="table-cell">Quantity</th>
                        <th scope="col" class="table-cell">Status</th>
                        <th scope="col" class="table-cell">Date</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    @foreach($returns as $return)
                        <tr class="table-row">
                            <td class="table-cell">{{ $return->id }}</td>
                            <td class="table-cell">{{ $return->product->ProductID ?? 'Unknown Product' }}</td>
                            <td class="table-cell">{{ $return->quantity }}</td>
                            <td class="table-cell status-{{ strtolower($return->return_status) }}">{{ $return->return_status }}</td>
                            <td class="table-cell">{{ \Carbon\Carbon::parse($return->created_at)->format('M. j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination Controls --}}
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                {{-- Use the appends() method to preserve query parameters like store_id when paginating --}}
                {{ $returns->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
            </ul>
        </nav>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/returnRequestList.css?v=6.5') }}">
@endsection
