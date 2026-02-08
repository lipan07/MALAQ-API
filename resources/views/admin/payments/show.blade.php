@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-credit-card"></i> Payment Details</h5>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Buyer</th>
                        <td>{{ $payment->user->name ?? '-' }} ({{ $payment->user->email ?? '-' }})</td>
                    </tr>
                    <tr>
                        <th>Product</th>
                        <td>{{ $payment->post->title ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @if($payment->street_address || $payment->city || $payment->pin_code || $payment->country)
                    <tr>
                        <th>Delivery Address</th>
                        <td>
                            @if($payment->street_address)<div>{{ $payment->street_address }}</div>@endif
                            @if($payment->city || $payment->pin_code)<div>{{ $payment->city }}{{ $payment->city && $payment->pin_code ? ' - ' : '' }}{{ $payment->pin_code }}</div>@endif
                            @if($payment->country)<div>{{ $payment->country }}</div>@endif
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($payment->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($payment->status === 'confirmed')
                                <span class="badge bg-success">Confirmed</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Submitted At</th>
                        <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @if($payment->admin_verified_at)
                    <tr>
                        <th>Verified At</th>
                        <td>{{ $payment->admin_verified_at->format('M d, Y H:i') }} by {{ $payment->adminVerifiedBy->name ?? '-' }}</td>
                    </tr>
                    @endif
                    @if($payment->admin_notes)
                    <tr>
                        <th>Admin Notes</th>
                        <td>{{ $payment->admin_notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div class="col-md-6">
                <h6>Payment Screenshot</h6>
                @if($screenshotUrl)
                    <a href="{{ $screenshotUrl }}" target="_blank" rel="noopener">
                        <img src="{{ $screenshotUrl }}" alt="Screenshot" class="img-fluid rounded border" style="max-height: 400px;">
                    </a>
                @else
                    <p class="text-muted">No screenshot uploaded.</p>
                @endif
            </div>
        </div>

        @if($payment->status === 'pending' && auth()->user()->canAccessPayments())
        <hr>
        <div class="d-flex flex-wrap gap-3 align-items-end">
            <form action="{{ route('admin.payments.confirm', $payment->id) }}" method="POST" class="d-flex align-items-center gap-2">
                @csrf
                <input type="text" name="admin_notes" class="form-control form-control-sm" style="width: 200px;" placeholder="Notes (optional)">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Confirm Payment Received</button>
            </form>
            <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST" class="d-flex align-items-center gap-2" onsubmit="return confirm('Reject this payment?');">
                @csrf
                <input type="text" name="admin_notes" class="form-control form-control-sm" style="width: 200px;" placeholder="Reason (optional)">
                <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle"></i> Reject</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
