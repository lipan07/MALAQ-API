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
        $payments = Payment::with(['user', 'post', 'adminVerifiedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.payments.index', compact('payments'));
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
