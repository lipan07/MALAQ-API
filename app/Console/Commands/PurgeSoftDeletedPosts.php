<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Models\Chat;
use Illuminate\Support\Facades\Storage;

class PurgeSoftDeletedPosts extends Command
{
    protected $signature = 'posts:purge-soft-deleted';
    protected $description = 'Permanently delete images and chats for soft-deleted posts';

    public function handle()
    {
        $posts = Post::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays(15))
            ->get();

        foreach ($posts as $post) {
            // Delete images from storage (images are now stored as JSON array in posts table)
            $images = $post->images ?? [];
            if (!empty($images) && is_array($images)) {
                foreach ($images as $imageUrl) {
                    // Handle both string URLs and object URLs
                    $url = is_string($imageUrl) ? $imageUrl : (is_object($imageUrl) && isset($imageUrl->url) ? $imageUrl->url : null);
                    if ($url) {
                        $relativePath = str_replace(config('app.url') . '/storage/', '', $url);
                        Storage::disk('public')->delete($relativePath);
                    }
                }
            }

            // Delete related chats and messages
            foreach ($post->chats as $chat) {
                // Delete messages
                foreach ($chat->messages as $message) {
                    $message->delete();
                }
                $chat->delete();
            }

            // Permanently delete the post
            $post->forceDelete();
        }

        $this->info('Purged all images and chats for soft-deleted posts.');
    }
}
