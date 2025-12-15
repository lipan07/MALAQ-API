<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate images from images table to posts table
        $postsWithImages = DB::table('images')
            ->where('imageable_type', 'App\Models\Post')
            ->select('imageable_id', 'url')
            ->get()
            ->groupBy('imageable_id');

        foreach ($postsWithImages as $postId => $images) {
            $imageUrls = $images->pluck('url')->toArray();
            DB::table('posts')
                ->where('id', $postId)
                ->update(['images' => json_encode($imageUrls)]);
        }

        // Migrate videos from videos table to posts table
        $postsWithVideos = DB::table('videos')
            ->where('videoable_type', 'App\Models\Post')
            ->select('videoable_id', 'url')
            ->get()
            ->groupBy('videoable_id');

        foreach ($postsWithVideos as $postId => $videos) {
            $videoUrls = $videos->pluck('url')->toArray();
            DB::table('posts')
                ->where('id', $postId)
                ->update(['videos' => json_encode($videoUrls)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is one-way. To reverse, you would need to recreate the images/videos records
        // which is complex and not recommended. If needed, restore from backup.
    }
};
