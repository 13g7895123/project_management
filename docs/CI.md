# CI/CD 持續整合與部署文件

## 概述

此專案使用 GitHub Actions 進行持續整合和持續部署 (CI/CD)。整個流程包括自動化測試、程式碼品質檢查、安全性掃描和自動部署。

## 工作流程架構

### 主要工作流程

1. **Backend Tests** - 後端 Laravel 應用程式測試
2. **Frontend Tests** - 前端 Nuxt.js 應用程式測試  
3. **Code Quality** - 程式碼品質和格式檢查
4. **Security Audit** - 安全性漏洞掃描
5. **Deploy** - 自動部署到 VPS

### 觸發條件

- **Push 到 main/master/develop 分支**: 執行完整的 CI/CD 流程
- **Pull Request 到 main/master**: 執行測試和程式碼品質檢查
- **只有 master 分支的 push**: 才會觸發部署

## 詳細流程說明

### 1. Backend Tests (後端測試)

**目的**: 確保 Laravel 後端應用程式的穩定性和正確性

**環境設置**:
- PHP 8.2
- MySQL 8.0 測試資料庫
- Composer 依賴管理
- XDebug 程式碼覆蓋率

**執行步驟**:
1. 安裝 PHP 和相關擴展
2. 快取和安裝 Composer 依賴
3. 設置測試環境變數
4. 執行資料庫遷移
5. 執行 PHPUnit 測試套件
6. 生成程式碼覆蓋率報告

**測試範圍**:
- Feature Tests: API 端點測試
- Unit Tests: 模型和服務邏輯測試
- Database Tests: 資料庫互動測試

**覆蓋率要求**: 最低 80%

### 2. Frontend Tests (前端測試)

**目的**: 確保 Nuxt.js 前端應用程式的功能正確性

**環境設置**:
- Node.js 18
- Vitest 測試框架
- Vue Test Utils
- Happy DOM 測試環境

**執行步驟**:
1. 安裝 Node.js 和 npm
2. 快取和安裝 npm 依賴
3. 執行 Vitest 測試套件
4. 生成程式碼覆蓋率報告

**測試範圍**:
- Composables 測試
- 元件測試
- API 互動測試
- 工具函數測試

### 3. Code Quality (程式碼品質)

**目的**: 維護程式碼品質和一致性

**檢查項目**:
- **PHP CS Fixer**: PHP 程式碼格式檢查
- **ESLint**: JavaScript/TypeScript 程式碼規範檢查
- **Vue 元件**: Vue 元件最佳實務檢查

**執行條件**: 
- 如果專案中有相應的設定檔案才會執行
- 失敗不會阻止部署，但會產生警告

### 4. Security Audit (安全性審計)

**目的**: 識別已知的安全性漏洞

**檢查範圍**:
- **Composer 依賴**: 檢查 PHP 套件安全性漏洞
- **npm 依賴**: 檢查 Node.js 套件安全性漏洞

**安全等級**: 
- Composer: 所有等級
- npm: 中等級別以上 (moderate+)

### 5. Deploy (自動部署)

**部署條件**:
- 所有測試都必須通過
- 只在 master 分支的 push 事件觸發
- 需要設置 SSH 連線憑證

**部署步驟**:
1. 連線到 VPS 伺服器
2. 拉取最新程式碼
3. 使用 Docker Compose 重建容器
4. 執行資料庫遷移
5. 快取 Laravel 設定
6. 執行健康檢查
7. 驗證部署狀態

## 環境配置

### 必要的 GitHub Secrets

在 GitHub 倉庫設定中需要配置以下 secrets:

```bash
SSH_PRIVATE_KEY    # SSH 私鑰 (用於連線 VPS)
SSH_HOST          # VPS 主機 IP 或域名
SSH_USER          # SSH 登入使用者名稱
```

### 本地開發環境

#### 後端測試環境設置

```bash
cd backend/src

# 安裝依賴
composer install

# 複製環境設定檔
cp .env.example .env.testing

# 設置測試資料庫
php artisan key:generate --env=testing

# 執行測試
php artisan test

# 執行詳細測試輸出
php artisan test -v
```

#### 前端測試環境設置

```bash
cd frontend/src

# 安裝依賴
npm install

# 執行測試
npm run test

# 執行帶覆蓋率的測試
npm run test:coverage
```

## 測試策略

### 後端測試策略

1. **Model Tests**: 測試資料模型的關聯和驗證
2. **Controller Tests**: 測試 API 路由和回應
3. **Service Tests**: 測試業務邏輯層
4. **Integration Tests**: 測試完整的資料流

### 前端測試策略

1. **Composable Tests**: 測試可復用的組合式函數
2. **Component Tests**: 測試 Vue 元件的行為
3. **Store Tests**: 測試 Pinia 狀態管理
4. **Integration Tests**: 測試頁面級別的互動

## 效能監控

### 測試效能指標

- **測試執行時間**: 監控測試套件執行時間
- **程式碼覆蓋率**: 維持最低覆蓋率標準
- **相依性安全性**: 追蹤安全性漏洞數量

### 部署效能指標

- **部署時間**: 監控完整部署流程時間
- **應用程式啟動時間**: 容器啟動和就緒時間
- **健康檢查**: API 端點回應時間

## 故障排除

### 常見問題

1. **測試失敗**:
   - 檢查測試資料庫連線
   - 確認環境變數設置
   - 檢查依賴套件版本

2. **部署失敗**:
   - 檢查 SSH 連線憑證
   - 確認 VPS 伺服器狀態
   - 檢查 Docker 容器記錄

3. **程式碼品質檢查失敗**:
   - 執行本地格式修正工具
   - 檢查 ESLint 和 PHP CS Fixer 設定

### 除錯命令

```bash
# 檢查 GitHub Actions 記錄
gh run list
gh run view [run-id]

# 本地除錯
docker-compose logs [service-name]
php artisan test -v
npm run test -- --reporter=verbose
```

## 最佳實務

### 程式碼提交

1. **提交前檢查**: 本地執行測試套件
2. **提交訊息**: 使用清楚的提交訊息格式
3. **小型提交**: 保持提交的原子性和獨立性

### 測試撰寫

1. **測試覆蓋率**: 新功能必須包含測試
2. **測試隔離**: 確保測試之間不相互影響
3. **模擬外部依賴**: 使用 Mock 隔離外部服務

### 部署管理

1. **藍綠部署**: 考慮實施零停機時間部署
2. **回滾策略**: 準備快速回滾機制
3. **監控告警**: 設置部署後的監控和告警

## 擴展和改進

### 計劃中的改進

1. **並行測試**: 提高測試執行速度
2. **容器化測試**: 使用 Docker 統一測試環境
3. **自動化效能測試**: 加入負載和效能測試
4. **多環境部署**: 支援 staging 和 production 環境

### 整合建議

1. **程式碼品質門檻**: 設置更嚴格的品質標準
2. **自動化依賴更新**: 使用 Dependabot 自動更新依賴
3. **通知整合**: 整合 Slack 或 Discord 通知

## 聯絡資訊

如有 CI/CD 相關問題，請：

1. 查看 GitHub Actions 執行記錄
2. 檢查此文件的故障排除章節
3. 聯絡開發團隊進行支援

---

**最後更新**: 2025-08-12  
**維護者**: Development Team