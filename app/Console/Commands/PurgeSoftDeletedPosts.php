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
            // Delete images from storage and DB
            foreach ($post->images as $image) {
                $relativePath = str_replace(config('app.url') . '/storage/', '', $image->url);
                Storage::disk('public')->delete($relativePath);
                $image->delete();
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
