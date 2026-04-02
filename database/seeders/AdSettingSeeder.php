<?php

namespace Database\Seeders;

use App\Models\AdSetting;
use Illuminate\Database\Seeder;

class AdSettingSeeder extends Seeder
{
    /**
     * Slugs must match Reuse-V3 (useAdSettings / isAdEnabled).
     */
    public function run(): void
    {
        $rows = [
            ['slug' => 'home_feed_native', 'label' => 'Home — feed native ad', 'is_enabled' => true, 'sort_order' => 10],
            ['slug' => 'following_feed_native', 'label' => 'Following — feed native ad', 'is_enabled' => true, 'sort_order' => 20],
            ['slug' => 'chat_list_native', 'label' => 'Chat list — native ad', 'is_enabled' => true, 'sort_order' => 30],
            ['slug' => 'product_details_native', 'label' => 'Product details — native ad', 'is_enabled' => true, 'sort_order' => 40],
            ['slug' => 'listing_type_native', 'label' => 'Listing type selection — native ad', 'is_enabled' => true, 'sort_order' => 50],
            ['slug' => 'subcategory_native', 'label' => 'Subcategory picker — native ad', 'is_enabled' => true, 'sort_order' => 60],
            ['slug' => 'chat_box_banner', 'label' => 'Chat thread — banner (composer)', 'is_enabled' => true, 'sort_order' => 70],
            ['slug' => 'my_ads_feed_banner', 'label' => 'My ads — inline feed banners', 'is_enabled' => true, 'sort_order' => 80],
            ['slug' => 'post_create_interstitial', 'label' => 'After new post — interstitial', 'is_enabled' => true, 'sort_order' => 90],
        ];

        foreach ($rows as $row) {
            AdSetting::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'label' => $row['label'],
                    'is_enabled' => $row['is_enabled'],
                    'sort_order' => $row['sort_order'],
                ]
            );
        }
    }
}
