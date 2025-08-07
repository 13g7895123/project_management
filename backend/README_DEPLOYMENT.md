# Backend Deployment Guide

## 生產環境部署

### 快速部署
```bash
# 直接運行部署腳本
./deploy-prod.sh
```

### 手動部署步驟

1. **環境設置**
```bash
# 複製環境配置
cp src/.env.example src/.env
# 編輯環境變數（特別是資料庫配置）
nano src/.env
```

2. **生成應用金鑰**
```bash
cd src
php artisan key:generate
```

3. **啟動服務**
```bash
# 使用生產環境配置
docker-compose -f docker-compose.prod.yml up -d
```

4. **執行資料庫遷移**
```bash
# 等待 MySQL 準備就緒後執行
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force
```

5. **優化應用程式**
```bash
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
```

## API 測試

### 健康檢查
```bash
curl https://project.mercylife.cc/api/health
```

預期回應：
```json
{
  "success": true,
  "message": "API is healthy!",
  "timestamp": "2024-01-01T00:00:00.000000Z",
  "app_name": "Laravel",
  "app_env": "production",
  "database": "connected",
  "clients_count": 2,
  "php_version": "8.2.x",
  "laravel_version": "10.x"
}
```

### 測試業主 API
```bash
# 獲取業主列表
curl https://project.mercylife.cc/api/clients

# 創建新業主
curl -X POST https://project.mercylife.cc/api/clients \
  -H "Content-Type: application/json" \
  -d '{
    "name": "測試業主",
    "how_we_met": "朋友介紹",
    "contacts": [
      {"type": "email", "value": "test@example.com", "is_primary": true}
    ]
  }'
```

## 故障排除

### 路由找不到錯誤 "The route clients could not be found"

可能原因：
1. **Nginx 代理配置**：確保 `/api` 請求正確代理到後端服務
2. **資料庫連接**：檢查 MySQL 服務是否正常運行
3. **應用程式未啟動**：檢查 Laravel 應用容器狀態

檢查步驟：
```bash
# 檢查容器狀態
docker-compose -f docker-compose.prod.yml ps

# 檢查應用程式日誌
docker-compose -f docker-compose.prod.yml logs app

# 檢查資料庫連接
docker-compose -f docker-compose.prod.yml exec app php artisan migrate:status

# 測試路由列表
docker-compose -f docker-compose.prod.yml exec app php artisan route:list --path=api
```

### 前端 Nginx 代理配置範例

如果前端和後端在同一域名下，需要在前端的 Nginx 配置中添加：

```nginx
# 代理 API 請求到後端服務
location /api/ {
    proxy_pass http://localhost:9018/api/;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
}
```

## 環境變數說明

重要的環境變數：
- `APP_PORT`: API 服務對外端口（預設：9018）
- `DB_HOST`: 資料庫主機（Docker 內為 mysql）
- `DB_DATABASE`: 資料庫名稱
- `DB_USERNAME`: 資料庫用戶名
- `DB_PASSWORD`: 資料庫密碼
- `MYSQL_EXTERNAL_PORT`: MySQL 對外端口（預設：3306）

## 常用命令

```bash
# 查看服務狀態
docker-compose -f docker-compose.prod.yml ps

# 重啟服務
docker-compose -f docker-compose.prod.yml restart

# 查看日誌
docker-compose -f docker-compose.prod.yml logs -f app

# 進入應用程式容器
docker-compose -f docker-compose.prod.yml exec app bash

# 清除快取
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker-compose -f docker-compose.prod.yml exec app php artisan config:clear
```