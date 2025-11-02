<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShareController extends Controller
{
    /**
     * Redirect to product deep link or show landing page
     * This handles web URLs like https://yourdomain.com/product/{id}
     */
    public function redirectToProduct($id)
    {
        try {
            $post = Post::with(['category', 'images'])->find($id);

            if (!$post) {
                return $this->renderNotFoundPage();
            }

            // Extract domain from APP_URL
            $appUrl = config('app.url');
            
            // Generate deep link URL
            $deepLink = "reuseapp://product/{$id}";
            
            // Generate web URL
            $webUrl = "{$appUrl}/product/{$id}";

            // Render HTML page with meta tags for social sharing
            return $this->renderProductSharePage($post, $deepLink, $webUrl);
        } catch (\Exception $e) {
            Log::error('Error in ShareController::redirectToProduct: ' . $e->getMessage());
            return $this->renderNotFoundPage();
        }
    }

    /**
     * Render product share page with Open Graph meta tags
     */
    private function renderProductSharePage($post, $deepLink, $webUrl)
    {
        $title = $post->title ?? 'Product on Reuse';
        $description = $this->getProductDescription($post);
        $imageUrl = $this->getProductImageUrl($post);
        $price = $post->amount ? '₹' . number_format($post->amount) : 'Price not specified';

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} - Reuse</title>
    
    <!-- Open Graph Meta Tags for Social Sharing -->
    <meta property="og:title" content="{$title}">
    <meta property="og:description" content="{$description}">
    <meta property="og:image" content="{$imageUrl}">
    <meta property="og:url" content="{$webUrl}">
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="Reuse">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{$title}">
    <meta name="twitter:description" content="{$description}">
    <meta name="twitter:image" content="{$imageUrl}">
    
    <!-- Fallback Meta Tags -->
    <meta name="description" content="{$description}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .image-container {
            width: 100%;
            height: 300px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-container .placeholder {
            font-size: 48px;
            color: #ccc;
        }
        .content {
            padding: 30px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        .price {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 15px;
        }
        .info {
            color: #666;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .description {
            color: #555;
            margin-top: 20px;
            line-height: 1.6;
        }
        .button-container {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .button {
            padding: 15px 30px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-block;
        }
        .button-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .button-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        .button-secondary:hover {
            background: #f8f9ff;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .app-badge {
            margin-top: 20px;
            text-align: center;
            color: #999;
            font-size: 12px;
        }
    </style>
    
    <script>
        // Try to open app, fallback to store if not installed
        function openApp() {
            var deepLink = '{$deepLink}';
            var fallbackUrl = 'https://play.google.com/store/apps/details?id=com.reuse'; // Update with your app store URL
            
            // Try to open the app
            window.location.href = deepLink;
            
            // If app doesn't open within 2 seconds, redirect to app store
            setTimeout(function() {
                window.location.href = fallbackUrl;
            }, 2000);
        }
        
        // Auto-detect if user is on mobile and try to open app
        window.onload = function() {
            var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            if (isMobile) {
                // Small delay before attempting to open app
                setTimeout(function() {
                    openApp();
                }, 500);
            }
        };
    </script>
</head>
<body>
    <div class="container">
        <div class="image-container">
            <img src="{$imageUrl}" alt="{$title}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="placeholder" style="display:none;">
                📦
            </div>
        </div>
        <div class="content">
            <h1 class="title">{$title}</h1>
            <div class="price">{$price}</div>
            
            {$this->renderProductInfo($post)}
            
            <div class="button-container">
                <a href="{$deepLink}" onclick="openApp(); return false;" class="button button-primary">
                    Open in Reuse App
                </a>
                <button onclick="openApp();" class="button button-secondary">
                    Download App
                </button>
            </div>
            
            <div class="app-badge">
                Viewing on web? Download our app for the best experience!
            </div>
        </div>
    </div>
</body>
</html>
HTML;

        return response($html)->header('Content-Type', 'text/html');
    }

    /**
     * Get product description for meta tags
     */
    private function getProductDescription($post)
    {
        $parts = [];
        
        if ($post->category) {
            $parts[] = "Category: " . $post->category->name;
        }
        
        if ($post->address) {
            $parts[] = "Location: " . $post->address;
        }
        
        if ($post->amount) {
            $parts[] = "Price: ₹" . number_format($post->amount);
        }
        
        return !empty($parts) ? implode(' | ', $parts) : 'Check out this product on Reuse!';
    }

    /**
     * Get product image URL for meta tags
     */
    private function getProductImageUrl($post)
    {
        if ($post->images && $post->images->count() > 0) {
            $firstImage = $post->images->first();
            // Ensure full URL
            if (strpos($firstImage->url, 'http') === 0) {
                return $firstImage->url;
            }
            return config('app.url') . '/storage/' . $firstImage->url;
        }
        
        // Default image or placeholder
        return config('app.url') . '/images/reuse-logo.png'; // Update with your logo path
    }

    /**
     * Render product info section
     */
    private function renderProductInfo($post)
    {
        $info = '<div class="description">';
        
        if ($post->category) {
            $info .= '<div class="info"><strong>Category:</strong> ' . htmlspecialchars($post->category->name) . '</div>';
        }
        
        if ($post->address) {
            $info .= '<div class="info"><strong>Location:</strong> ' . htmlspecialchars($post->address) . '</div>';
        }
        
        $info .= '</div>';
        
        return $info;
    }

    /**
     * Render 404 page
     */
    private function renderNotFoundPage()
    {
        $appUrl = htmlspecialchars(config('app.url'));
        
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Not Found - Reuse</title>
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
        <p><a href="{$appUrl}">Go to Reuse</a></p>
    </div>
</body>
</html>
HTML;

        return response($html, 404)->header('Content-Type', 'text/html');
    }

    /**
     * Track product share (optional analytics endpoint)
     */
    public function trackShare(Request $request, $id)
    {
        try {
            $post = Post::find($id);
            
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // Log the share event
            Log::info('Product shared', [
                'post_id' => $id,
                'user_id' => $request->user_id ?? null,
                'platform' => $request->platform ?? 'unknown',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // You can add share tracking to database here if needed
            // For example, increment a share_count column or create a shares table

            return response()->json([
                'success' => true,
                'message' => 'Share tracked successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error tracking share: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to track share'
            ], 500);
        }
    }
}

