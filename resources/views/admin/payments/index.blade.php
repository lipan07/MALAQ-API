@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-credit-card"></i> Payment List</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Buyer</th>
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $index => $payment)
                    <tr>
                        <td>{{ $payments->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $payment->user->name ?? '-' }}</strong><br>
                            <small class="text-muted">{{ $payment->user->email ?? '-' }}</small>
                        </td>
                        <td>
                            <strong>{{ $payment->post->title ?? '-' }}</strong><br>
                            <small class="text-muted">ID: {{ Str::limit($payment->post_id, 8) }}</small>
                        </td>
                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                        <td>
                            @if($payment->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($payment->status === 'confirmed')
                                <span class="badge bg-success">Confirmed</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No payments yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('admin.partials.per-page-pagination', ['paginator' => $payments, 'perPage' => $perPage ?? 15])
    </div>
</div>
@endsection
