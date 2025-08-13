<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebsiteSettingsController extends Controller
{
    /**
     * Get all website settings
     */
    public function index()
    {
        try {
            $settings = WebsiteSettings::getAllSettings();
            
            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '獲取網站設定失敗',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update website settings
     */
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'multilingual_enabled' => 'sometimes|boolean',
                'dark_mode_enabled' => 'sometimes|boolean',
                'search_enabled' => 'sometimes|boolean',
                'notifications_enabled' => 'sometimes|boolean',
                'footer_enabled' => 'sometimes|boolean',
                'time_enabled' => 'sometimes|boolean',
                'website_primary_name' => 'sometimes|string|max:255',
                'website_secondary_name' => 'sometimes|string|max:255',
                'logo_data' => 'sometimes|nullable|string',
                'favicon_data' => 'sometimes|nullable|string',
                'theme_mode' => 'sometimes|string|in:light,dark,system',
                'primary_color' => 'sometimes|string|max:7',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => '驗證失敗',
                    'errors' => $validator->errors()
                ], 422);
            }

            $settingsMap = [
                'multilingual_enabled' => ['type' => 'boolean', 'description' => '啟用多語系功能'],
                'dark_mode_enabled' => ['type' => 'boolean', 'description' => '啟用深色模式'],
                'search_enabled' => ['type' => 'boolean', 'description' => '啟用搜尋功能'],
                'notifications_enabled' => ['type' => 'boolean', 'description' => '啟用通知功能'],
                'footer_enabled' => ['type' => 'boolean', 'description' => '顯示頁尾'],
                'time_enabled' => ['type' => 'boolean', 'description' => '顯示時間'],
                'website_primary_name' => ['type' => 'string', 'description' => '網站主要名稱'],
                'website_secondary_name' => ['type' => 'string', 'description' => '網站副名稱'],
                'logo_data' => ['type' => 'text', 'description' => 'Logo 圖片資料'],
                'favicon_data' => ['type' => 'text', 'description' => 'Favicon 資料'],
                'theme_mode' => ['type' => 'string', 'description' => '主題模式'],
                'primary_color' => ['type' => 'string', 'description' => '主要顏色'],
            ];

            foreach ($request->all() as $key => $value) {
                if (isset($settingsMap[$key])) {
                    WebsiteSettings::setSetting(
                        $key,
                        $value,
                        $settingsMap[$key]['type'],
                        $settingsMap[$key]['description']
                    );
                }
            }

            return response()->json([
                'success' => true,
                'message' => '網站設定更新成功',
                'data' => WebsiteSettings::getAllSettings()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '更新網站設定失敗',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific setting
     */
    public function show($key)
    {
        try {
            $setting = WebsiteSettings::getSetting($key);
            
            if ($setting === null) {
                return response()->json([
                    'success' => false,
                    'message' => '設定項目不存在'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'key' => $key,
                    'value' => $setting
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '獲取設定失敗',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset settings to default values
     */
    public function resetDefaults()
    {
        try {
            $defaultSettings = [
                'multilingual_enabled' => false,
                'dark_mode_enabled' => true,
                'search_enabled' => true,
                'notifications_enabled' => true,
                'footer_enabled' => true,
                'time_enabled' => false,
                'website_primary_name' => '專案管理系統',
                'website_secondary_name' => 'Project Management',
                'logo_data' => '',
                'favicon_data' => '',
                'theme_mode' => 'system',
                'primary_color' => '#6366f1',
            ];

            $settingsMap = [
                'multilingual_enabled' => ['type' => 'boolean', 'description' => '啟用多語系功能'],
                'dark_mode_enabled' => ['type' => 'boolean', 'description' => '啟用深色模式'],
                'search_enabled' => ['type' => 'boolean', 'description' => '啟用搜尋功能'],
                'notifications_enabled' => ['type' => 'boolean', 'description' => '啟用通知功能'],
                'footer_enabled' => ['type' => 'boolean', 'description' => '顯示頁尾'],
                'time_enabled' => ['type' => 'boolean', 'description' => '顯示時間'],
                'website_primary_name' => ['type' => 'string', 'description' => '網站主要名稱'],
                'website_secondary_name' => ['type' => 'string', 'description' => '網站副名稱'],
                'logo_data' => ['type' => 'text', 'description' => 'Logo 圖片資料'],
                'favicon_data' => ['type' => 'text', 'description' => 'Favicon 資料'],
                'theme_mode' => ['type' => 'string', 'description' => '主題模式'],
                'primary_color' => ['type' => 'string', 'description' => '主要顏色'],
            ];

            foreach ($defaultSettings as $key => $value) {
                WebsiteSettings::setSetting(
                    $key,
                    $value,
                    $settingsMap[$key]['type'],
                    $settingsMap[$key]['description']
                );
            }

            return response()->json([
                'success' => true,
                'message' => '設定已重設為預設值',
                'data' => WebsiteSettings::getAllSettings()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '重設設定失敗',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}