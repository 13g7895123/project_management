<?php

namespace Database\Seeders;

use App\Models\WebsiteSettings;
use Illuminate\Database\Seeder;

class WebsiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            [
                'key' => 'multilingual_enabled',
                'value' => '0',
                'type' => 'boolean',
                'description' => '啟用多語系功能'
            ],
            [
                'key' => 'dark_mode_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => '啟用深色模式'
            ],
            [
                'key' => 'search_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => '啟用搜尋功能'
            ],
            [
                'key' => 'notifications_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => '啟用通知功能'
            ],
            [
                'key' => 'footer_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => '顯示頁尾'
            ],
            [
                'key' => 'website_primary_name',
                'value' => '專案管理系統',
                'type' => 'string',
                'description' => '網站主要名稱'
            ],
            [
                'key' => 'website_secondary_name',
                'value' => 'Project Management',
                'type' => 'string',
                'description' => '網站副名稱'
            ],
            [
                'key' => 'logo_data',
                'value' => '',
                'type' => 'text',
                'description' => 'Logo 圖片資料 (Base64)'
            ],
            [
                'key' => 'favicon_data',
                'value' => '',
                'type' => 'text',
                'description' => 'Favicon 資料 (Base64)'
            ],
        ];

        foreach ($defaultSettings as $setting) {
            WebsiteSettings::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Website settings seeded successfully.');
    }
}