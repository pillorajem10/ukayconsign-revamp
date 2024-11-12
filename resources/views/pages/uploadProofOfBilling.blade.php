@extends('layout') <!-- Extending your main layout -->

@section('title', 'Upload Proof of Payment')

@section('content')
    <div class="upload-proof-container">
        <h1 class="upload-proof-title">Upload Proof of Payment</h1>
        <p class="upload-proof-description">Please select your payment platform and upload the proof of payment for billing #{{ $billing->id }}.</p>

        <form action="{{ route('billings.updatePayment', $billing->id) }}" method="POST" enctype="multipart/form-data" class="upload-proof-form">
            @csrf
            @method('PUT')

            <!-- Payment Platform -->
            <div class="form-group">
                <label for="payment_platform" class="form-label">Payment Platform</label>
                <select name="payment_platform" id="payment_platform" class="form-select" required>
                    <option value="Gcash" {{ old('payment_platform', $billing->payment_platform) == 'Gcash' ? 'selected' : '' }}>Gcash</option>
                    <option value="Bank Transfer" {{ old('payment_platform', $billing->payment_platform) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                </select>
                @error('payment_platform')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Proof of Payment -->
            <div class="form-group">
                <label for="proof_of_payment" class="form-label">Proof of Payment</label>
                <input type="file" name="proof_of_payment" id="proof_of_payment" class="form-input" accept=".jpg,.jpeg,.png,.pdf,.docx" required>
                @error('proof_of_payment')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-submit">Submit</button>
        </form>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/uploadProofOfBilling.css') }}"> <!-- Include your custom styles here -->
@endsection
