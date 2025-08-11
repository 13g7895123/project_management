# Docker 前後端整合計畫 - TODO 清單

## 專案概覽

本專案目前包含獨立的前端（Nuxt 3）和後端（Laravel 11）Docker 配置。經過分析，我們可以將兩個服務整合到統一的 Docker Compose 架構中，提供更好的開發體驗和部署流程。

## 當前架構分析

### 後端服務（Laravel）
- **容器**: Laravel App (PHP-FPM + Nginx), MySQL 8.0, Redis, phpMyAdmin
- **埠口**: APP:9018, MySQL:9118, Redis:9218, phpMyAdmin:9318
- **網路**: `laravel` bridge network
- **特點**: 包含完整的 LAMP 堆疊和資料庫管理工具

### 前端服務（Nuxt 3）
- **容器**: Frontend Production, Frontend Dev (可選)
- **埠口**: Production:3000, Development:3001
- **網路**: `frontend-network` bridge network
- **特點**: 多階段構建，支持開發和生產環境

### 當前問題
1. 前後端使用獨立的網路，無法直接通訊
2. 開發時需要手動啟動兩個 Docker Compose
3. API 路由配置分散在多個地方
4. 缺乏統一的環境變數管理

## 🎯 整合建議與執行項目

### 階段一：基礎整合（高優先級）

#### ✅ 1. 建立統一的 Docker Compose 根配置
- [ ] 創建根目錄 `docker-compose.yml` 整合前後端服務
- [ ] 建立共享網路 `app-network` 供所有服務使用
- [ ] 設定統一的環境變數檔案 `.env` 在根目錄

#### ✅ 2. 設計服務間通訊架構
- [ ] 後端服務保持內部埠口 `8000`，外部埠口可配置
- [ ] 前端服務內部埠口 `3000`，外部埠口可配置
- [ ] 設定服務間主機名解析（frontend -> backend）

#### ✅ 3. API 路由統一配置
```yaml
# 建議的服務配置
frontend:
  environment:
    - NUXT_PUBLIC_API_BASE_URL=http://backend:8000/api
backend:
  container_name: backend
```

#### ✅ 4. 建立開發和生產環境配置
- [ ] `docker-compose.yml` - 基礎配置
- [ ] `docker-compose.dev.yml` - 開發環境覆蓋
- [ ] `docker-compose.prod.yml` - 生產環境覆蓋

### 階段二：開發體驗優化（中優先級）

#### ✅ 5. 熱重載配置
- [ ] 前端開發模式支援 Hot Module Replacement
- [ ] 後端代碼變更自動重啟（開發環境）
- [ ] 設定 volumes 映射以便實時編輯

#### ✅ 6. 舊檔案處理和遷移
- [ ] 備份現有的 docker-compose 檔案
  - `backend/docker-compose.yml` → `backend/docker-compose.yml.backup`
  - `frontend/docker-compose.yml` → `frontend/docker-compose.yml.backup`
- [ ] 建立遷移指南文檔 `MIGRATION.md`
- [ ] 建立快速回滾腳本 `scripts/rollback.sh`
- [ ] 更新 README 檔案說明新的啟動方式

## 🛠️ 實施計畫

### 建議的目錄結構
```
project_management/
├── docker-compose.yml              # 統一的 compose 配置
├── docker-compose.dev.yml          # 開發環境覆蓋
├── docker-compose.prod.yml         # 生產環境覆蓋
├── .env                             # 統一環境變數
├── scripts/
│   ├── dev.sh                       # 開發環境啟動
│   ├── deploy.sh                    # 生產環境部署
│   └── rollback.sh                  # 快速回滾腳本
├── MIGRATION.md                     # 詳細遷移指南
├── backend/
│   ├── Dockerfile
│   ├── src/                         # Laravel 代碼
│   └── docker-compose.yml.backup    # 原始檔案備份
└── frontend/
    ├── Dockerfile
    ├── src/                         # Nuxt 代碼
    └── docker-compose.yml.backup    # 原始檔案備份
```

### 統一的環境變數配置範例
```env
# =============================================================================
# 應用程式配置
# =============================================================================
COMPOSE_PROJECT_NAME=project_management
APP_ENV=development

# =============================================================================
# 網路配置
# =============================================================================
FRONTEND_PORT=3000
BACKEND_PORT=8000
MYSQL_PORT=3306
REDIS_PORT=6379
PHPMYADMIN_PORT=8080

# =============================================================================
# 外部埠口配置
# =============================================================================
EXTERNAL_FRONTEND_PORT=3000
EXTERNAL_BACKEND_PORT=8000
EXTERNAL_MYSQL_PORT=3306
EXTERNAL_REDIS_PORT=6379
EXTERNAL_PHPMYADMIN_PORT=8080

# =============================================================================
# API 配置
# =============================================================================
API_BASE_URL=http://localhost/api
FRONTEND_API_URL=http://backend:8000/api
```

## ⚠️ 重要注意事項

### 向後相容性與舊檔案處理
- **檔案備份策略**：
  - 將 `backend/docker-compose.yml` 重命名為 `docker-compose.yml.backup`
  - 將 `frontend/docker-compose.yml` 重命名為 `docker-compose.yml.backup`
  - 保留所有現有的 Dockerfile 和環境配置
- **遷移指南**：
  - 建立詳細的 `MIGRATION.md` 文檔說明整合步驟
  - 包含新舊配置的對照表和啟動命令變更
  - 提供常見問題和解決方案
- **回滾計畫**：
  - 建立 `scripts/rollback.sh` 腳本快速恢復舊配置
  - 確保可以在 5 分鐘內回到原始狀態
  - 提供完整的回滾驗證檢查清單

### 安全考量
- 資料庫密碼使用 Docker secrets 或環境變數
- 敏感配置檔案不應包含在映像中
- 設定適當的網路隔離和訪問控制

### 效能優化
- 使用 Docker layer caching 加速構建
- 配置適當的資源限制
- 考慮使用 Docker Buildx 支援多平台構建

## 🚀 執行順序

### 立即開始（Week 1）
1. 建立統一的 docker-compose.yml
2. 設定共享網路和基本服務間通訊
3. 測試前後端整合

### 短期目標（Week 2-3）
4. 完善開發環境配置
5. 設定熱重載和開發體驗優化
6. 舊檔案處理和遷移指南建立

## 📋 檢查清單

### 整合前準備
- [ ] 備份現有配置檔案
- [ ] 測試當前獨立服務運行正常
- [ ] 確認所有依賴和版本相容性

### 整合後驗證
- [ ] 前端可以成功調用後端 API
- [ ] 資料庫連接和資料持久性正常
- [ ] 開發和生產環境都能正常運行
- [ ] 所有現有功能保持正常

### 舊檔案處理驗證
- [ ] 確認備份檔案建立成功
- [ ] 測試回滾腳本功能正常
- [ ] 驗證遷移指南的完整性和正確性
- [ ] 確認所有團隊成員了解新的工作流程

## 🎉 預期效益

### 開發體驗改善
- 一鍵啟動整個開發環境
- 統一的配置管理
- 更好的服務間通訊
- 簡化的故障排除

### 部署流程優化
- 統一的部署腳本
- 一致的環境配置
- 更好的版本控制
- 簡化的回滾流程

### 維護成本降低
- 減少配置檔案重複
- 統一的監控和日志
- 更好的文檔和標準化
- 降低學習成本

---

*建議優先實施階段一的項目，確保基本整合功能正常後再進行其他階段的開發。*