<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InviteToken;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'post', 'adminVerifiedBy'])->orderBy('created_at', 'desc');

        $statusFilter = null;
        if ($request->filled('status') && in_array($request->status, ['pending', 'confirmed', 'rejected'], true)) {
            $query->where('status', $request->status);
            $statusFilter = $request->status;
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 15;
        $payments = $query->paginate($perPage)->withQueryString();

        $counts = [
            'all' => Payment::count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'confirmed' => Payment::where('status', 'confirmed')->count(),
            'rejected' => Payment::where('status', 'rejected')->count(),
        ];

        return view('admin.payments.index', compact('payments', 'perPage', 'statusFilter', 'counts'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'post', 'adminVerifiedBy']);
        $screenshotUrl = $payment->screenshot_path
            ? asset('storage/' . $payment->screenshot_path)
            : null;

        return view('admin.payments.show', [
            'payment' => $payment,
            'screenshotUrl' => $screenshotUrl,
        ]);
    }

    public function confirm(Request $request, Payment $payment)
    {
        if (!Auth::user()->canAccessPayments()) {
            abort(403, 'You do not have permission to confirm payments.');
        }
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $payment->update([
            'status' => 'confirmed',
            'admin_verified_at' => now(),
            'admin_verified_by' => Auth::id(),
            'admin_notes' => $request->admin_notes,
        ]);

        // Activate invite tokens for this user (so they can share and others can register with them)
        InviteToken::where('user_id', $payment->user_id)->update(['is_active' => true]);

        return back()->with('success', 'Payment confirmed successfully.');
    }

    public function reject(Request $request, Payment $payment)
    {
        if (!Auth::user()->canAccessPayments()) {
            abort(403, 'You do not have permission to reject payments.');
        }
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $payment->update([
            'status' => 'rejected',
            'admin_verified_at' => now(),
            'admin_verified_by' => Auth::id(),
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Payment rejected.');
    }
}
