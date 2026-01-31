<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Store payment with screenshot (API - authenticated user)
     */
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|uuid|exists:posts,id',
            'amount' => 'required|numeric|min:0',
            'screenshot' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'pin_code' => 'required|string|size:6|regex:/^[0-9]{6}$/',
            'country' => 'required|string|max:100',
        ]);

        try {
            $user = Auth::user();
            $post = Post::findOrFail($request->post_id);

            $file = $request->file('screenshot');
            $path = $file->store('payment_screenshots', 'public');

            $payment = Payment::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'amount' => $request->amount,
                'payment_method' => 'qr_code',
                'screenshot_path' => $path,
                'street_address' => $request->street_address,
                'city' => $request->city,
                'pin_code' => $request->pin_code,
                'country' => $request->country,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment details submitted successfully.',
                'payment' => [
                    'id' => $payment->id,
                    'post_id' => $payment->post_id,
                    'amount' => $payment->amount,
                    'status' => $payment->status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Payment store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit payment. Please try again.',
            ], 500);
        }
    }
}
