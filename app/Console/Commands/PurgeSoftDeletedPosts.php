<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PurgeSoftDeletedPosts extends Command
{
    protected $signature = 'posts:purge-soft-deleted';

    protected $description = 'Permanently delete media, post_contents, and chats for soft-deleted posts';

    public function handle()
    {
        $posts = Post::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays(15))
            ->get();

        foreach ($posts as $post) {
            foreach ($post->chats as $chat) {
                foreach ($chat->messages as $message) {
                    $message->delete();
                }
                $chat->delete();
            }

            // Post::booted removes B2/local files from post_contents before the row is hard-deleted (FK cascade).
            $post->forceDelete();
        }

        $this->info('Purged media, post_contents, and chats for soft-deleted posts.');
    }
}
