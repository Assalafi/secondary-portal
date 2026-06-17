@extends('layouts.parent')

@section('title', 'Application Payment')
@section('page-title', 'Application Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.admission.index') }}" class="text-decoration-none">Applications</a></li>
                <li class="breadcrumb-item active text-muted">Payment</li>
            </ol>
        </nav>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Payment Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded p-3">
                            <i class="ri-money-dollar-circle-line text-primary" style="font-size: 24px;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-1">Application Fee Payment</h5>
                        <p class="text-muted small mb-0">Application No: <strong>{{ $application->application_number }}</strong></p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Invoice Information -->
                @if(isset($invoice) && $invoice)
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="ri-file-list-3-line me-2" style="font-size: 20px;"></i>
                        <h6 class="mb-0">Invoice Details</h6>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Invoice Number (RRR)</small>
                            <strong id="invoice-number">{{ $invoice->invoice_number }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-warning">{{ $invoice->status }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Due Date</small>
                            <strong>{{ $invoice->due_date->format('d M, Y') }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Balance</small>
                            <strong class="text-danger">₦{{ number_format($invoice->balance, 2) }}</strong>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Payment Summary -->
                <div class="bg-light rounded p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Application Fee</span>
                        <span class="h5 mb-0">₦{{ number_format($applicationFee, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total Amount</span>
                        <span class="h4 mb-0 text-primary">₦{{ number_format($applicationFee, 2) }}</span>
                    </div>
                </div>

                <!-- Remita Payment -->
                <div class="alert alert-primary">
                    <h6 class="mb-2"><i class="ri-information-line me-2"></i>Secure Payment via Remita</h6>
                    <p class="mb-0 small">You will be redirected to Remita's secure payment gateway to complete your payment.</p>
                </div>

                <!-- Terms -->
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree that the application fee is non-refundable and I accept the
                            <a href="#" class="text-decoration-none">terms and conditions</a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-flex gap-3">
                    <button type="button" onclick="openPaymentWidget()" class="btn btn-primary btn-lg px-5" id="payBtn">
                        <i class="ri-secure-payment-line me-2"></i>Pay Now - ₦{{ number_format($applicationFee, 2) }}
                    </button>
                    <a href="{{ route('parent.admission.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>

                <!-- Manual Verification Option -->
                <hr class="my-4">
                <div class="text-center">
                    <p class="text-muted small mb-2">Already paid through bank/USSD?</p>
                    <button type="button" onclick="verifyPayment('{{ $rrr }}', '{{ $orderId }}')" class="btn btn-sm btn-outline-primary" id="verifyBtn">
                        <i class="ri-check-line me-1"></i>Verify Payment Status
                    </button>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body p-4">
                <h6 class="mb-3"><i class="ri-question-line me-2"></i>Need Help?</h6>
                <p class="text-muted small mb-0">
                    If you encounter any issues with payment, please contact the school's accounts department at
                    <a href="mailto:{{ $globalSettings['email'] ?? 'accounts@school.com' }}">{{ $globalSettings['email'] ?? 'accounts@school.com' }}</a>
                    or call {{ $globalSettings['phone_number'] ?? '+234 800 000 0000' }}
                </p>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<!-- Remita Payment Widget -->
<script src="{{ config('remita.widget_url') }}"></script>

<script>
    // Payment details passed from controller
    const RRR = '{{ $rrr }}';
    const ORDER_ID = '{{ $orderId }}';
    const PUBLIC_KEY = '{{ config("remita.public_key") }}';
    const AMOUNT = {{ $applicationFee }};

    function openPaymentWidget() {
        const termsCheckbox = document.getElementById('terms');
        
        if (!termsCheckbox.checked) {
            alert('Please accept the terms and conditions to proceed.');
            return;
        }

        const payBtn = document.getElementById('payBtn');
        payBtn.disabled = true;
        payBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Opening Gateway...';

        console.log('Opening Remita payment widget with RRR:', RRR);
        
        try {
            // Initialize Remita payment widget with existing RRR
            const paymentEngine = RmPaymentEngine.init({
                key: PUBLIC_KEY,
                processRrr: true,
                transactionId: ORDER_ID,
                extendedData: {
                    customFields: [
                        {
                            name: "rrr",
                            value: RRR
                        }
                    ]
                },
                onSuccess: function(response) {
                    console.log('Payment successful', response);
                    verifyPayment(RRR, ORDER_ID);
                },
                onError: function(response) {
                    console.log('Payment error', response);
                    alert('Payment failed. Please try again.');
                    window.location.reload();
                },
                onClose: function() {
                    console.log('Payment window closed');
                    window.location.reload();
                }
            });
            
            paymentEngine.showPaymentWidget();
        } catch (error) {
            console.error('Error initializing payment widget:', error);
            alert('Failed to open payment gateway. Please refresh and try again.');
            window.location.reload();
        }
    }

    function verifyPayment(rrr, orderId) {
        console.log('Verifying payment with RRR:', rrr);
        
        // Show loading state
        const verifyBtn = document.getElementById('verifyBtn');
        const originalHTML = verifyBtn.innerHTML;
        verifyBtn.disabled = true;
        verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Verifying...';
        
        fetch('/parent/payments/remita/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                rrr: rrr,
                orderId: orderId
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Verification response:', data);
            
            if (data.success) {
                alert('✅ Payment verified successfully!');
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.href = '{{ route("parent.admission.form", $application->id) }}';
                }
            } else if (data.pending) {
                // Payment is pending (not yet completed)
                alert('⏳ ' + data.message + '\n\nStatus: ' + data.status + '\n\nPlease complete the payment in the Remita widget, or try verifying again after payment is completed.');
                // Restore button - let user try again
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = originalHTML;
            } else {
                // Payment failed or other error
                alert('❌ ' + (data.message || 'Payment verification failed') + '\n\nStatus: ' + (data.status || 'Unknown'));
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = originalHTML;
                setTimeout(() => location.reload(), 2000);
            }
        })
        .catch(error => {
            console.error('Verification error:', error);
            alert('Verification error. Please contact support with RRR: ' + rrr);
            // Restore button
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = originalHTML;
            setTimeout(() => location.reload(), 2000);
        });
    }

</script>
@endpush
@endsection
