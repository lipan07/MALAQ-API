<?php

namespace App\Console\Commands;

use App\Models\Chat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurgeArchivedChats extends Command
{
    protected $signature = 'chats:purge-archived';

    protected $description = 'Permanently delete chats where both participants archived them, 30+ days after the later deletion';

    public function handle(): int
    {
        $cutoff = now()->subDays(30);
        $purged = 0;

        Chat::query()
            ->whereNotNull('buyer_deleted_at')
            ->whereNotNull('seller_deleted_at')
            ->orderBy('updated_at')
            ->chunk(50, function ($chats) use ($cutoff, &$purged) {
                foreach ($chats as $chat) {
                    $laterDeletion = $chat->buyer_deleted_at->gt($chat->seller_deleted_at)
                        ? $chat->buyer_deleted_at
                        : $chat->seller_deleted_at;

                    if ($laterDeletion->gt($cutoff)) {
                        continue;
                    }

                    try {
                        DB::transaction(function () use ($chat) {
                            try {
                                $chat->images()->delete();
                            } catch (\Throwable $e) {
                                Log::warning('PurgeArchivedChats: images delete skipped', [
                                    'chat_id' => $chat->id,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                            $chat->messages()->delete();
                            $chat->delete();
                        });
                        $purged++;
                    } catch (\Throwable $e) {
                        Log::error('PurgeArchivedChats: failed', [
                            'chat_id' => $chat->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });

        $this->info("Purged {$purged} chat(s).");

        return self::SUCCESS;
    }
}
