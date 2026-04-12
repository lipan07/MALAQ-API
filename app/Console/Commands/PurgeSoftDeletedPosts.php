<?php

namespace App\Console\Commands;

use App\Enums\PostContentType;
use App\Models\Post;
use App\Services\BackblazeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeSoftDeletedPosts extends Command
{
    protected $signature = 'posts:purge-soft-deleted';

    protected $description = 'Permanently delete media, post_contents, and chats for soft-deleted posts';

    public function handle()
    {
        $posts = Post::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays(15))
            ->get();

        $backblaze = app(BackblazeService::class);

        foreach ($posts as $post) {
            $post->load('contents');

            foreach ($post->contents as $row) {
                if ($row->type === PostContentType::Video) {
                    try {
                        if ($row->backblaze_id) {
                            $backblaze->deleteVideo($row->backblaze_id);
                        } elseif ($row->url && str_contains($row->url, 'backblazeb2.com')) {
                            $backblaze->deleteVideoByUrl($row->url);
                        }
                    } catch (\Throwable $e) {
                        $this->warn("B2 delete failed for post {$post->id}: {$e->getMessage()}");
                    }
                } elseif ($row->type === PostContentType::Image && $row->url && str_contains($row->url, '/storage/')) {
                    $relativePath = str_replace(config('app.url') . '/storage/', '', $row->url);
                    Storage::disk('public')->delete($relativePath);
                }
            }

            $post->contents()->delete();

            foreach ($post->chats as $chat) {
                foreach ($chat->messages as $message) {
                    $message->delete();
                }
                $chat->delete();
            }

            $post->forceDelete();
        }

        $this->info('Purged media, post_contents, and chats for soft-deleted posts.');
    }
}
