# 專案管理系統 - 整合版 Docker 配置

這個專案已經整合了前後端的 Docker 配置，提供更簡單的開發和部署體驗。

## 🏗️ 架構概覽

- **前端**: Nuxt 3 + Pinia + Tailwind CSS
- **後端**: Laravel 11 + MySQL 8.0 + Redis
- **容器化**: Docker + Docker Compose
- **網路**: 統一的 `app-network` 供所有服務通訊

## 🚀 快速開始

### 開發環境

```bash
# 使用整合的開發環境
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d

# 或使用便捷腳本
./scripts/dev.sh
```

### 生產環境

```bash
# 使用整合的生產環境
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# 或使用便捷腳本
./scripts/prod.sh
```

## 📊 服務端口配置

| 服務 | 開發端口 | 生產端口 | 內部端口 |
|------|----------|----------|----------|
| 前端 | 3000 | 3000 | 3000 |
| 後端 | 8000 | 8000 | 8000 |
| MySQL | 3306 | - | 3306 |
| Redis | 6379 | - | 6379 |
| phpMyAdmin | 8080 | - | 80 |

## 🌐 訪問地址

### 開發環境
- 前端應用: http://localhost:3000
- 後端 API: http://localhost:8000/api
- phpMyAdmin: http://localhost:8080

### 生產環境
- 前端應用: http://localhost:3000
- 後端 API: http://localhost:8000/api

## 🔧 環境配置

主要的環境變數配置在根目錄的 `.env` 檔案中：

```env
# 專案名稱
COMPOSE_PROJECT_NAME=project_management

# 外部端口配置
EXTERNAL_FRONTEND_PORT=3000
EXTERNAL_BACKEND_PORT=8000
EXTERNAL_MYSQL_PORT=3306
EXTERNAL_REDIS_PORT=6379
EXTERNAL_PHPMYADMIN_PORT=8080

# 資料庫配置
DB_DATABASE=project_management
DB_USERNAME=laravel
DB_PASSWORD=laravel_password
MYSQL_ROOT_PASSWORD=root_password
```

## 📁 檔案結構

```
project_management/
├── docker-compose.yml              # 基礎配置
├── docker-compose.dev.yml          # 開發環境覆蓋
├── docker-compose.prod.yml         # 生產環境覆蓋
├── .env                             # 統一環境變數
├── scripts/
│   ├── dev.sh                       # 開發環境啟動腳本
│   ├── prod.sh                      # 生產環境部署腳本
│   └── rollback.sh                  # 快速回滾腳本
├── backend/
│   ├── src/                         # Laravel 原始碼
│   ├── Dockerfile
│   └── docker-compose.yml.backup    # 原始配置備份
└── frontend/
    ├── src/                         # Nuxt 原始碼
    ├── Dockerfile
    ├── Dockerfile.dev
    └── docker-compose.yml.backup    # 原始配置備份
```

## 🔄 服務間通訊

在新的整合配置中，服務間通訊使用 Docker 內部網路：

- 前端連接後端: `http://backend:8000/api`
- 後端連接資料庫: `mysql:3306`
- 後端連接快取: `redis:6379`

## 📋 常用命令

### 啟動服務
```bash
# 開發環境
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d

# 生產環境
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

### 查看日誌
```bash
# 所有服務
docker-compose logs -f

# 特定服務
docker-compose logs -f frontend
docker-compose logs -f backend
```

### 執行 Laravel 命令
```bash
# 進入後端容器
docker-compose exec backend bash

# 執行 Artisan 命令
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan db:seed
```

### 停止服務
```bash
# 停止並移除容器
docker-compose -f docker-compose.yml -f docker-compose.dev.yml down

# 同時移除資料卷（注意：會刪除資料庫資料）
docker-compose -f docker-compose.yml -f docker-compose.dev.yml down -v
```

## 🔙 回滾到舊配置

如果需要回到原本的獨立配置：

```bash
# 使用回滾腳本
./scripts/rollback.sh

# 手動啟動原本的服務
cd backend && docker-compose up -d
cd frontend && docker-compose up -d
```

## ⚠️ 重要注意事項

1. **資料持久化**: MySQL 和 Redis 資料會持久化在 Docker 資料卷中
2. **環境區分**: 開發和生產環境有不同的安全配置
3. **網路隔離**: 生產環境中資料庫端口不對外開放
4. **備份檔案**: 原始的 docker-compose.yml 檔案已備份為 `.backup`

## 🐛 問題排解

### 常見問題

1. **端口衝突**: 檢查 `.env` 中的端口設定
2. **服務無法啟動**: 查看 `docker-compose logs` 輸出
3. **資料庫連接問題**: 確認 MySQL 服務已完全啟動
4. **前端 API 調用失敗**: 檢查網路配置和服務名稱

### 檢查服務狀態
```bash
docker-compose ps
docker-compose logs service_name
```

## 📈 效能優化

- 生產環境已配置資源限制
- 使用 Docker 多階段構建優化映像大小
- 開發環境支援熱重載
- 生產環境移除開發依賴和除錯工具