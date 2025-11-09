# LINE 登入功能設定指南

本系統已整合 LINE Login 功能，允許用戶使用 LINE 帳號登入系統。

## 功能特色

- ✅ 使用 LINE 帳號快速登入
- ✅ 自動建立新用戶或連結現有帳號
- ✅ 安全的 OAuth 2.0 認證流程
- ✅ 支援解除 LINE 帳號綁定
- ✅ 與現有密碼登入系統並存

## 前置準備

### 1. 建立 LINE Login Channel

1. 前往 [LINE Developers Console](https://developers.line.biz/console/)
2. 登入您的 LINE 帳號
3. 建立新的 Provider（如果還沒有）
4. 在 Provider 下建立新的 Channel
   - Channel Type: 選擇 "LINE Login"
   - 填寫必要資訊（App name, App description 等）

### 2. 設定 Channel 資訊

在 LINE Login Channel 設定頁面中：

#### Basic settings
- 記下您的 **Channel ID**（用於 `LINE_CLIENT_ID`）
- 記下您的 **Channel Secret**（用於 `LINE_CLIENT_SECRET`）

#### LINE Login settings
設定 **Callback URL**：
```
http://localhost:8000/api/auth/line/callback  # 開發環境
https://your-domain.com/api/auth/line/callback  # 正式環境
```

#### 權限設定（Scopes）
確保已啟用以下權限：
- `profile` - 取得用戶的顯示名稱、用戶 ID、個人資料圖片
- `openid` - 使用者識別
- `email` - 取得用戶的電子郵件地址（選用）

## 後端配置

### 1. 環境變數設定

在 `backend/.env` 檔案中添加以下設定：

```env
# LINE Login Configuration
LINE_CLIENT_ID=your_channel_id_here
LINE_CLIENT_SECRET=your_channel_secret_here
LINE_REDIRECT_URI=http://localhost:8000/api/auth/line/callback
```

**重要說明：**
- `LINE_CLIENT_ID`: 從 LINE Developers Console 取得的 Channel ID
- `LINE_CLIENT_SECRET`: 從 LINE Developers Console 取得的 Channel Secret
- `LINE_REDIRECT_URI`: 必須與 LINE Console 中設定的 Callback URL 完全一致

### 2. 資料庫遷移

執行資料庫遷移以添加 LINE 相關欄位：

```bash
cd backend/src
php artisan migrate
```

這會在 `users` 資料表中添加：
- `line_id` - 儲存 LINE 用戶 ID（唯一）
- `line_access_token` - 儲存 LINE access token（加密）
- 同時將 `email` 和 `password` 欄位設為可選（nullable）

## 前端配置

前端無需額外配置，已自動從後端 API 取得設定。

## 使用流程

### 用戶登入流程

1. 用戶在登入頁面點擊「使用 LINE 登入」按鈕
2. 系統重定向到 LINE 授權頁面
3. 用戶在 LINE 授權頁面同意授權
4. LINE 重定向回系統的 callback URL
5. 系統處理回調：
   - 如果 LINE ID 已存在：直接登入該用戶
   - 如果 Email 已存在：連結 LINE 到現有帳號
   - 如果都不存在：建立新用戶帳號
6. 登入成功，重定向到首頁

### 帳號連結邏輯

- **已有帳號用戶**：使用相同 Email 的現有帳號會自動連結到 LINE
- **新用戶**：首次使用 LINE 登入會自動建立新帳號
- **用戶名稱生成**：自動從 LINE 顯示名稱生成唯一用戶名

## API 端點

### 1. 開始 LINE 登入
```
GET /api/auth/line
```
重定向到 LINE 授權頁面

### 2. LINE 回調處理
```
GET /api/auth/line/callback
```
處理 LINE 授權回調，返回用戶資料和 token

### 3. 解除 LINE 綁定
```
DELETE /api/auth/line/unlink
```
需要認證。解除當前用戶的 LINE 帳號綁定

**注意：** 只有在用戶已設定密碼的情況下才能解除 LINE 綁定，以確保用戶仍能登入系統。

## 安全性考量

1. **Token 加密**：LINE access token 在資料庫中以加密形式儲存
2. **HTTPS 必須**：正式環境必須使用 HTTPS
3. **CSRF 保護**：使用 state 參數防止 CSRF 攻擊
4. **密碼備援**：建議用戶設定密碼作為備用登入方式

## 測試

### 開發環境測試

1. 確保 LINE Channel 的 Callback URL 包含開發環境 URL
2. 啟動後端服務：`docker-compose up -d`
3. 啟動前端服務：`npm run dev`
4. 訪問登入頁面並點擊「使用 LINE 登入」

### 常見問題排解

#### 問題：「redirect_uri mismatch」錯誤
**解決方案**：
- 確認 `.env` 中的 `LINE_REDIRECT_URI` 與 LINE Console 設定完全一致
- 確認包含正確的協議（http/https）和端口號

#### 問題：無法取得用戶 Email
**解決方案**：
- 在 LINE Console 確認已啟用 `email` scope
- 注意：某些 LINE 用戶可能未設定 Email

#### 問題：「Channel ID or Secret is invalid」
**解決方案**：
- 確認 `.env` 中的 `LINE_CLIENT_ID` 和 `LINE_CLIENT_SECRET` 正確
- 確認沒有多餘的空格或換行符號

## 正式環境部署

1. 在 LINE Console 新增正式環境的 Callback URL
2. 更新 `.env` 檔案中的設定：
   ```env
   LINE_REDIRECT_URI=https://your-domain.com/api/auth/line/callback
   ```
3. 確保使用 HTTPS
4. 重新部署應用程式

## 相關資源

- [LINE Login 官方文件](https://developers.line.biz/en/docs/line-login/)
- [LINE Developers Console](https://developers.line.biz/console/)
- [Laravel Socialite 文件](https://laravel.com/docs/socialite)
- [SocialiteProviders LINE](https://socialiteproviders.com/Line/)

## 支援與回饋

如有任何問題或建議，請聯繫開發團隊。
