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

#### ✅ 6. 統一腳本管理
- [ ] 創建 `scripts/` 目錄
- [ ] 開發啟動腳本 `scripts/dev.sh`
- [ ] 生產部署腳本 `scripts/deploy.sh`
- [ ] 資料庫初始化腳本 `scripts/init-db.sh`

#### ✅ 7. 日志統一管理
- [ ] 配置 ELK Stack 或 Loki (可選)
- [ ] 設定日志卷映射
- [ ] 統一日志格式和輪轉策略

### 階段三：生產環境優化（低優先級）

#### ✅ 8. 健康檢查配置
- [ ] 為所有服務添加健康檢查
- [ ] 設定服務依賴關係和啟動順序
- [ ] 配置重啟策略

#### ✅ 9. 監控和指標收集
- [ ] 整合 Prometheus + Grafana
- [ ] 設定應用程式指標收集
- [ ] 配置告警規則

#### ✅ 10. 備份和恢復策略
- [ ] 自動化資料庫備份
- [ ] 應用程式狀態備份
- [ ] 災害恢復流程文檔

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
│   └── init-db.sh                   # 資料庫初始化
├── backend/
│   ├── Dockerfile
│   ├── src/                         # Laravel 代碼
│   └── docker-compose.yml           # 保留作為參考
└── frontend/
    ├── Dockerfile
    ├── src/                         # Nuxt 代碼
    └── docker-compose.yml           # 保留作為參考
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

### 向後相容性
- 保留現有的 `backend/docker-compose.yml` 和 `frontend/docker-compose.yml` 作為參考
- 確保現有的環境變數和配置不會被破壞
- 提供遷移指南和回滾計畫

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
5. 建立部署腳本和統一腳本管理
6. 設定熱重載和開發體驗優化

### 中期目標（Week 4-6）
7. 日志統一管理配置
8. 健康檢查和服務依賴配置
9. 完善文檔和測試

### 長期目標（Month 2+）
10. 監控和指標收集
11. 備份和災害恢復
12. CI/CD 整合

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

### 效能測試
- [ ] 服務啟動時間測試
- [ ] API 回應時間測試
- [ ] 資源使用率監控
- [ ] 負載測試（如果需要）

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