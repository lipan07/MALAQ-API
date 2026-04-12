<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('post_id')->constrained('posts')->cascadeOnDelete();
            $table->string('type', 16); // image | video
            $table->string('backblaze_id', 128)->nullable();
            $table->text('url');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['post_id', 'type']);
            $table->index(['post_id', 'sort_order']);
        });

        if (Schema::hasColumn('posts', 'images') || Schema::hasColumn('posts', 'videos')) {
            $this->migrateJsonMediaToPostContents(
                Schema::hasColumn('posts', 'images'),
                Schema::hasColumn('posts', 'videos')
            );
        }

        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'images')) {
                $table->dropColumn('images');
            }
            if (Schema::hasColumn('posts', 'videos')) {
                $table->dropColumn('videos');
            }
        });
    }

    private function migrateJsonMediaToPostContents(bool $hasImagesColumn, bool $hasVideosColumn): void
    {
        DB::table('posts')->orderBy('id')->chunk(200, function ($posts) use ($hasImagesColumn, $hasVideosColumn) {
            foreach ($posts as $post) {
                $sort = 0;
                if ($hasImagesColumn && isset($post->images) && $post->images !== null) {
                    $images = json_decode($post->images, true);
                    if (is_array($images)) {
                        foreach ($images as $url) {
                            if (!is_string($url) || $url === '') {
                                continue;
                            }
                            DB::table('post_contents')->insert([
                                'id' => (string) Str::uuid(),
                                'post_id' => $post->id,
                                'type' => 'image',
                                'backblaze_id' => null,
                                'url' => $url,
                                'sort_order' => $sort++,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
                if ($hasVideosColumn && isset($post->videos) && $post->videos !== null) {
                    $videos = json_decode($post->videos, true);
                    if (is_array($videos)) {
                        foreach ($videos as $url) {
                            if (!is_string($url) || $url === '') {
                                continue;
                            }
                            DB::table('post_contents')->insert([
                                'id' => (string) Str::uuid(),
                                'post_id' => $post->id,
                                'type' => 'video',
                                'backblaze_id' => null,
                                'url' => $url,
                                'sort_order' => $sort++,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->json('images')->nullable()->after('amount');
            $table->json('videos')->nullable()->after('images');
        });

        if (Schema::hasTable('post_contents')) {
            DB::table('posts')->orderBy('id')->chunk(200, function ($posts) {
                foreach ($posts as $post) {
                    $rows = DB::table('post_contents')->where('post_id', $post->id)->orderBy('sort_order')->get();
                    $images = [];
                    $videos = [];
                    foreach ($rows as $row) {
                        if ($row->type === 'image') {
                            $images[] = $row->url;
                        } elseif ($row->type === 'video') {
                            $videos[] = $row->url;
                        }
                    }
                    DB::table('posts')->where('id', $post->id)->update([
                        'images' => !empty($images) ? json_encode(array_values($images)) : null,
                        'videos' => !empty($videos) ? json_encode(array_values($videos)) : null,
                    ]);
                }
            });
            Schema::dropIfExists('post_contents');
        }
    }
};
