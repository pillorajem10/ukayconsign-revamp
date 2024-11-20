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

        <div class="back-to-dashboard">
            <a href="{{ route('stores.index') }}" class="btn btn-secondary">Back to Stores</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover return-request-table">
                <thead class="table-header">
                    <tr>
                        <th scope="col" class="table-cell">Return ID</th>
                        <th scope="col" class="table-cell">Product</th>
                        <th scope="col" class="table-cell">Quantity</th>
                        <th scope="col" class="table-cell">Status</th>
                        <th scope="col" class="table-cell">Date</th>
                        <th scope="col" class="table-cell">Action</th> <!-- New Action column -->
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
                            <td class="table-cell">
                                @if($return->return_status != 'Received By Store') <!-- Only show button if status is not 'Received By Store' -->
                                    <form action="{{ route('usc-returns.receivedBack', $return->id) }}" method="POST" 
                                            onsubmit="return confirm('Are you sure you already received back the new items?')">
                                        @csrf
                                        @method('PUT') <!-- Spoof PUT request -->
                                        
                                        <!-- Hidden input to pass store_id -->
                                        <input type="hidden" name="store_id" value="{{ $return->store_id }}">
                                        
                                        <button class="btn btn-primary btn-received-back">Received Back</button>
                                    </form>                          
                                @else
                                    <span class="badge badge-success">Received</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination Controls --}}
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                {{ $returns->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
            </ul>
        </nav>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/returnRequestList.css?v=7.4') }}">
@endsection
