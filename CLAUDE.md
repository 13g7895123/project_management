# 專案管理系統

## 專案概述
這是一個專為自由工作者設計的專案管理系統，主要用於管理業主資訊、專案追蹤和收入統計。系統採用前後端分離架構，前端使用 Nuxt 3，後端使用 Laravel 11。

## 核心功能
- **業主管理**：管理業主基本資訊、聯繫方式和認識經過
- **專案管理**：完整的專案生命週期管理，包含接洽、執行、完成、收款等狀態
  - 支援訂金管理功能：可設定是否收取訂金、訂金金額及收款日期
  - 專案狀態包含待評估、已接洽、進行中、已完成、已收款
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
  - 包含訂金管理欄位：requires_deposit（是否收取訂金）、deposit_amount（訂金金額）、deposit_received_date（訂金收款日期）
  - 支援待評估狀態，該狀態下專案金額可為空
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

## 版本控制指示

### Point 20 - Git 提交規範
關於 point 20：add commit push，記得不要加上「Generated with Claude Code」的標記

**執行步驟**：
1. `git add` - 添加變更檔案到暫存區
2. `git commit` - 提交變更，使用有意義的提交訊息
3. `git push` - 推送到遠端儲存庫

**重要規則**：
- **絕對不要**在提交訊息中添加「Generated with Claude Code」標記
- 使用清晰、描述性的提交訊息
- 遵循專案的提交訊息格式慣例

**範例**：
```bash
git add .
git commit -m "feat: add user management functionality"
git push origin main
```

**禁止範例**：
```bash
# ❌ 錯誤：包含 Claude Code 標記
git commit -m "feat: add user management functionality

🤖 Generated with Claude Code"

# ✅ 正確：清晰的提交訊息
git commit -m "feat: add user management functionality"
```

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

### 前後端分離測試策略
關於 point 87：前後端分離時，前端專案沒有 API，測試應該怎麼調整？

**測試層級架構**：

#### 1. 單元測試 (Unit Tests)
**範圍**：測試獨立的函數和組件邏輯
**方法**：
- 使用 Mock API 請求（如現有的 `vi.fn()` 和 `vi.mock()`）
- 測試 Composables 的業務邏輯
- 測試元件的本地狀態和方法
- 測試工具函數和輔助方法

**範例**：
```javascript
// 測試 useProjects composable
it('should handle API errors gracefully', () => {
  mockApi.get.mockRejectedValue(new Error('Network error'))
  // 測試錯誤處理邏輯
})
```

#### 2. 整合測試 (Integration Tests)
**範圍**：測試多個組件之間的協作
**方法**：
- 使用 Mock 後端服務或 JSON Server
- 測試完整的用戶流程（如登入、建立專案）
- 測試狀態管理和組件間通訊

**工具建議**：
```bash
# 使用 MSW (Mock Service Worker) 模擬 API
npm install -D msw
# 或使用 JSON Server 提供假 API
npm install -D json-server
```

#### 3. 端對端測試 (E2E Tests)
**範圍**：測試完整的應用流程
**方法**：
- 使用測試環境的真實 API
- 自動化測試用戶操作路徑
- 驗證前後端整合正確性

**工具選擇**：
```bash
# Cypress 或 Playwright 用於 E2E 測試
npm install -D cypress
# 或
npm install -D @playwright/test
```

**測試環境配置**：

#### 開發環境測試
```javascript
// vitest.config.js
export default {
  test: {
    environment: 'happy-dom',
    setupFiles: ['./tests/setup.ts'],
    mockReset: true,
  }
}

// tests/setup.ts - Mock 所有 API 請求
global.$fetch = vi.fn()
global.useRuntimeConfig = vi.fn(() => ({
  public: { apiBaseUrl: 'http://localhost:8000/api' }
}))
```

#### CI/CD 環境測試
```yaml
# .github/workflows/ci-cd.yml
- name: Run frontend tests with mock APIs
  run: |
    npm run test:unit     # 單元測試（完全 Mock）
    npm run test:integration  # 整合測試（假 API）

- name: Run E2E tests (optional)
  run: |
    # 只在有測試環境 API 時執行
    if [[ "${{ secrets.TEST_API_URL }}" ]]; then
      npm run test:e2e
    fi
```

**實作建議**：

1. **分層測試金字塔**：
   - 70% 單元測試（快速、獨立）
   - 20% 整合測試（模擬 API 互動）
   - 10% E2E 測試（真實環境驗證）

2. **API 測試策略**：
   - **開發階段**：使用 Mock 和假數據
   - **CI/CD 階段**：使用獨立的測試 API 環境
   - **部署前驗證**：使用 staging 環境進行 E2E 測試

3. **測試資料管理**：
   ```javascript
   // tests/fixtures/mockData.js
   export const mockProjects = [
     { id: 1, name: 'Test Project', status: 'active' }
   ]
   
   // tests/mocks/api.js
   export const setupApiMocks = () => {
     vi.mocked($fetch).mockImplementation((url) => {
       if (url.includes('/projects')) return { data: mockProjects }
       // 其他 API 端點 Mock
     })
   }
   ```

**現有專案優勢**：
- 已建立完整的 Mock 架構
- Composables 設計良好，易於測試
- 測試隔離性佳，執行速度快
- CI/CD 流程已整合前端測試

## 工作流程規範

### Point 131 - 自動提交規範
關於 point 131：每次任務完成，如果有連結git repo，都幫我依據規則提交

**執行原則**：
- 每次完成功能或修復問題後，都應該進行git提交
- 遵循一致的提交訊息格式
- 確保變更內容完整且有意義

**提交流程**：
1. **檢查變更**：使用 `git status` 和 `git diff` 確認變更內容
2. **暫存變更**：使用 `git add .` 或指定檔案路徑
3. **提交變更**：使用有意義的提交訊息
4. **推送變更**：使用 `git push` 推送到遠端儲存庫

**提交訊息格式**：
```
<type>: <description>

[optional body]
```

**類型說明**：
- `feat`: 新功能
- `fix`: 錯誤修復
- `refactor`: 代碼重構
- `style`: 樣式調整
- `docs`: 文件更新
- `test`: 測試相關
- `chore`: 維護工作

**範例**：
```bash
git add .
git commit -m "feat: implement comprehensive settings and data import/export functionality"
git push origin main
```

**重要提醒**：
- **絕對不要**添加「Generated with Claude Code」標記
- 確保提交訊息清晰描述變更內容
- 每次任務完成都應執行此流程