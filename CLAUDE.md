# 專案管理系統

## 專案概述
這是一個專為自由工作者設計的專案管理系統，主要用於管理業主資訊、專案追蹤和收入統計。系統採用前後端分離架構，前端使用 Nuxt 3，後端使用 Laravel 11。

## 核心功能
- **業主管理**：管理業主基本資訊、聯繫方式和認識經過
- **專案管理**：完整的專案生命週期管理，包含接洽、執行、完成、收款等狀態
- **統計分析**：提供日、週、月、年等不同時間維度的接案數量和收入統計
- **儀表板**：視覺化展示專案概覽、收入趨勢和業務分析

## 技術架構

### 前端 (Nuxt 3)
- 框架：Nuxt 3 + Pinia 狀態管理
- UI：Nuxt UI + Tailwind CSS
- 圖表：Chart.js
- 表單驗證：VeeValidate
- 日期處理：Day.js

### 後端 (Laravel 11)
- 框架：Laravel 11
- 資料庫：MySQL 8.0
- 認證：Laravel Sanctum
- API 查詢：Spatie Laravel Query Builder
- 資料匯出：Laravel Excel

### 部署環境
- 容器化：Docker + Docker Compose
- 快取：Redis

## 資料庫設計

### 核心資料表
- **users**: 用戶資料表（支援未來多用戶擴展）
- **clients**: 業主資料表
- **contact_methods**: 聯繫方式表（支援多種聯繫方式）
- **projects**: 專案資料表
- **project_milestones**: 專案里程碑表
- **dashboard_stats**: 統計資料快取表

## 開發階段

### 第一階段：環境建置
- Laravel 後端環境與 Docker 配置
- 資料庫結構建立
- API 基礎架構設定
- 前端模板整合

### 第二階段：核心功能
- 業主管理 CRUD 功能
- 專案管理 CRUD 功能
- API 整合與資料驗證

### 第三階段：進階功能
- 儀表板統計功能
- 搜尋、篩選、排序功能
- 資料匯出功能

### 第四階段：優化部署
- 效能優化
- 測試與 Bug 修復
- 部署配置

## 特色功能
- 支援大量專案數據處理（千筆以上）
- 彈性的時間統計報表
- 預留多用戶支援架構
- 響應式設計適配各種裝置

## 安全性考量
- API 限流機制
- 資料驗證與清理
- 認證與授權管理
- 安全的 Docker 配置

## 重要提醒
請不要在每次提交時都加上「Generated with Claude Code」的標記。

## CSS 樣式指示

### 側邊欄收縮樣式
不要在 `main.css` 中加入以下 CSS 樣式：
```css
.sidebar-collapsed {
  width: var(--sidebar-collapsed-width);
}
```

此樣式應避免添加到主要樣式表中。

## 測試配置指示

### 前端測試 API 配置
關於 point 85：前端測試是否可以直接使用真實的API網址？

**不建議**在前端單元測試中使用真實 API 網址，理由如下：

1. **測試隔離性**：單元測試應該是獨立的，不應依賴外部服務
2. **測試速度**：網路請求會大幅降低測試執行速度
3. **測試穩定性**：網路問題或 API 服務異常會導致測試不穩定
4. **資料污染**：真實 API 調用可能會影響線上資料

**建議做法**：
- **單元測試**：繼續使用 Mock 的方式（如現有的 `vi.fn()` 和 `vi.mock()`）
- **整合測試**：可以考慮使用真實 API，但應該：
  - 使用測試專用的資料庫和 API 環境
  - 在 CI/CD 流程中確保後端服務可用
  - 使用測試資料，不影響生產環境

**現有配置**：
- 測試中使用 `mockFetch` 模擬 API 請求
- `setup.ts` 中配置 `apiBaseUrl: 'http://localhost:8000/api'`
- 建議保持現有 Mock 配置，確保測試的可靠性和速度

### 後端測試 PHPUnit 配置
關於 point 86：後端測試出現 PHPUnit "--verbose" 選項錯誤

**問題原因**：PHPUnit 10.x 版本中移除了 `--verbose` 選項

**解決方案**：
- 將 `php artisan test --verbose` 改為 `php artisan test -v`
- PHPUnit 10 支援的詳細輸出選項：
  - `-v`: 詳細輸出
  - `-vv`: 更詳細輸出  
  - `-vvv`: 除錯級別輸出

**已修復位置**：
- `.github/workflows/ci-cd.yml`: CI/CD 流程中的測試命令
- `docs/CI.md`: 文件中的範例命令