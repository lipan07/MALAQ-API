<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BuyController extends Controller
{
    /**
     * Show buying page for a product
     */
    public function show($id)
    {
        try {
            $post = Post::with(['category', 'user'])->find($id);

            if (!$post) {
                return $this->renderNotFoundPage();
            }

            $appUrl = config('app.url', 'https://nearx.co');
            $price = $post->amount ?? (is_object($post->post_details) ? ($post->post_details->amount ?? 0) : 0);
            $productTitle = $post->title ?? 'Product';
            $productImage = $this->getProductImageUrl($post);

            return view('buy.index', compact('post', 'appUrl', 'price', 'productTitle', 'productImage'));
        } catch (\Exception $e) {
            Log::error('Error in BuyController::show: ' . $e->getMessage());
            return $this->renderNotFoundPage();
        }
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, $id)
    {
        $request->validate([
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:google_pay,other',
        ]);

        try {
            $post = Post::findOrFail($id);
            $price = $post->amount ?? (is_object($post->post_details) ? ($post->post_details->amount ?? 0) : 0);

            // Here you would integrate with Google Pay API
            // For now, we'll just log the payment request
            Log::info('Payment request', [
                'post_id' => $id,
                'price' => $price,
                'address' => $request->address,
                'payment_method' => $request->payment_method,
            ]);

            // TODO: Integrate with actual Google Pay payment gateway
            // After successful payment, you would:
            // 1. Create an order record
            // 2. Update post status if needed
            // 3. Send notifications

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'order_id' => 'ORD-' . time(), // Temporary order ID
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Get product image URL
     */
    private function getProductImageUrl($post)
    {
        $images = $post->images ?? [];
        if (!empty($images) && is_array($images) && count($images) > 0) {
            $firstImage = $images[0];
            if (is_string($firstImage) && strpos($firstImage, 'http') === 0) {
                return $firstImage;
            }
            if (is_object($firstImage) && isset($firstImage->url)) {
                $imageUrl = $firstImage->url;
                if (strpos($imageUrl, 'http') === 0) {
                    return $imageUrl;
                }
                return config('app.url', 'https://nearx.co') . '/storage/' . $imageUrl;
            }
            if (is_string($firstImage)) {
                if (strpos($firstImage, 'http') === 0) {
                    return $firstImage;
                }
                return config('app.url', 'https://nearx.co') . '/storage/' . $firstImage;
            }
        }
        return config('app.url', 'https://nearx.co') . '/images/reuse-logo.png';
    }

    /**
     * Render 404 page
     */
    private function renderNotFoundPage()
    {
        $appUrl = htmlspecialchars(config('app.url', 'https://nearx.co'));

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Not Found - nearX</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .container {
            max-width: 500px;
        }
        h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            margin-bottom: 30px;
        }
        a {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>Product not found or has been removed.</p>
        <p><a href="{$appUrl}">Go to nearX</a></p>
    </div>
</body>
</html>
HTML;

        return response($html, 404)->header('Content-Type', 'text/html');
    }
}
