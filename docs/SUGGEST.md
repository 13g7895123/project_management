# 專案管理系統開發建議

基於 PROMPT.md 中的需求分析，以下是完整的專案開發建議：

## 需求澄清與確認

基於您的回覆，以下是確認的需求規格：

1. **用戶角色**：✅ **已確認**
   - 主要為單一管理員使用
   - 需預留多用戶支援架構（未來擴展用）
   - 建議：實作基礎的權限管理系統框架

2. **資料量級**：✅ **已確認**
   - 採用最大彈性設計
   - 支援大量專案數據（千筆以上）
   - 建議：使用資料庫索引優化和分頁機制

3. **報表需求**：✅ **已確認**
   - 時間頻率統計：日、週、月、年接案數量
   - 收入統計：各時間段的接案金額分析
   - 專案類別統計：各類型專案的分布情況
   - 建議：實作可互動的圖表和資料匯出功能

4. **整合需求**：✅ **已確認**
   - 目前無需外部系統整合
   - 建議：預留 API 擴展接口以備未來需求

## 1. 功能架構建議

### 前端架構（基於現有 Nuxt 3 模板）
```
專案管理系統
├── 儀表板 (Dashboard)
│   ├── 專案統計概覽
│   ├── 收入趨勢圖表
│   ├── 專案狀態分布
│   └── 近期活動時間軸
├── 專案管理 (Projects)
│   ├── 專案列表（支援篩選、排序、搜尋）
│   ├── 專案詳情
│   ├── 專案新增/編輯
│   └── 專案狀態管理
├── 業主管理 (Clients)
│   ├── 業主列表
│   ├── 業主詳情
│   ├── 業主新增/編輯
│   └── 聯繫記錄
└── 系統設定 (Settings)
    ├── 個人資料
    ├── 主題設定
    └── 資料備份
```

### 後端架構（Laravel）
```
backend/
├── app/
│   ├── Models/
│   │   ├── Project.php
│   │   ├── Client.php
│   │   └── ContactMethod.php
│   ├── Http/Controllers/API/
│   │   ├── ProjectController.php
│   │   ├── ClientController.php
│   │   └── DashboardController.php
│   └── Services/
│       ├── ProjectService.php
│       └── StatisticsService.php
├── database/migrations/
└── docker-compose.yml
```

## 2. 資料庫設計建議

### 核心資料表設計（已優化）

```sql
-- 用戶表（支援未來多用戶擴展）
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- 業主表
CREATE TABLE clients (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL COMMENT '所屬用戶',
    name VARCHAR(255) NOT NULL COMMENT '稱呼',
    how_we_met TEXT COMMENT '認識方式',
    notes TEXT COMMENT '備註',
    is_active BOOLEAN DEFAULT TRUE COMMENT '是否有效',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_name (name),
    INDEX idx_active (is_active)
);

-- 聯繫方式表（支援多種聯繫方式）
CREATE TABLE contact_methods (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    client_id BIGINT NOT NULL,
    type VARCHAR(50) NOT NULL COMMENT '聯繫方式類型（phone、email、line、wechat、telegram等）',
    value VARCHAR(255) NOT NULL COMMENT '聯繫方式值',
    is_primary BOOLEAN DEFAULT FALSE COMMENT '是否為主要聯繫方式',
    is_active BOOLEAN DEFAULT TRUE COMMENT '是否有效',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    INDEX idx_client_id (client_id),
    INDEX idx_type (type),
    INDEX idx_primary (is_primary)
);

-- 專案表（已優化索引）
CREATE TABLE projects (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL COMMENT '所屬用戶',
    client_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL COMMENT '專案名稱',
    description TEXT COMMENT '專案描述',
    category ENUM('website', 'script', 'server', 'custom') NOT NULL COMMENT '專案類別',
    amount DECIMAL(12,2) NOT NULL COMMENT '專案金額（提高精度）',
    contact_date DATE COMMENT '接洽日期',
    start_date DATE COMMENT '開始執行日期',
    completion_date DATE COMMENT '完成日期',
    payment_date DATE COMMENT '收款日期',
    status ENUM('contacted', 'in_progress', 'completed', 'paid') DEFAULT 'contacted',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium' COMMENT '優先級',
    is_active BOOLEAN DEFAULT TRUE COMMENT '是否有效',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_client_id (client_id),
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_dates (contact_date, start_date, completion_date, payment_date),
    INDEX idx_amount (amount),
    INDEX idx_active (is_active)
);

-- 專案里程碑表（用於詳細專案追蹤）
CREATE TABLE project_milestones (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    project_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    due_date DATE,
    completed_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id),
    INDEX idx_due_date (due_date),
    INDEX idx_completed (completed_at)
);

-- 統計數據緩存表（提升儀表板性能）
CREATE TABLE dashboard_stats (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    stat_type VARCHAR(50) NOT NULL COMMENT '統計類型',
    stat_period VARCHAR(20) NOT NULL COMMENT '統計周期（daily、weekly、monthly、yearly）',
    stat_date DATE NOT NULL COMMENT '統計日期',
    value DECIMAL(15,2) NOT NULL COMMENT '統計值',
    metadata JSON COMMENT '額外數據',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_stat (user_id, stat_type, stat_period, stat_date),
    INDEX idx_user_period (user_id, stat_period),
    INDEX idx_date (stat_date)
);
```

## 3. API 設計建議

### RESTful API 端點設計

```php
// 認證相關
POST /api/auth/login           // 用戶登入
POST /api/auth/logout          // 用戶登出
POST /api/auth/refresh         // 刷新 token
GET /api/auth/user             // 獲取當前用戶資料

// 儀表板統計（已優化）
GET /api/dashboard/stats
Response: {
    "overview": {
        "total_projects": 15,
        "total_revenue": 150000,
        "active_clients": 8,
        "projects_in_progress": 3
    },
    "charts": {
        "revenue_trend": {
            "daily": [...],
            "weekly": [...],
            "monthly": [...],
            "yearly": [...]
        },
        "projects_by_status": {
            "contacted": 2,
            "in_progress": 3,
            "completed": 8,
            "paid": 2
        },
        "projects_by_category": {
            "website": 8,
            "script": 3,
            "server": 2,
            "custom": 2
        }
    },
    "recent_activities": [
        {
            "type": "project_created",
            "project_name": "客戶網站",
            "client_name": "ABC公司",
            "amount": 50000,
            "date": "2024-01-15"
        }
    ]
}

// 時間統計 API
GET /api/dashboard/stats/period?type=revenue&period=monthly&year=2024
GET /api/dashboard/stats/period?type=projects&period=weekly&year=2024

// 專案管理（已擴展）
GET /api/projects              // 專案列表（支援分頁、篩選、排序）
  ?page=1&per_page=15&status=in_progress&category=website&client_id=1
GET /api/projects/{id}         // 專案詳情
POST /api/projects             // 新增專案
PUT /api/projects/{id}         // 更新專案
DELETE /api/projects/{id}      // 刪除專案
PATCH /api/projects/{id}/status // 更新專案狀態
GET /api/projects/export       // 匯出專案資料

// 業主管理（已擴展）
GET /api/clients               // 業主列表（支援搜尋、分頁）
  ?page=1&per_page=15&search=關鍵字
GET /api/clients/{id}          // 業主詳情
GET /api/clients/{id}/projects // 業主相關專案
POST /api/clients              // 新增業主
PUT /api/clients/{id}          // 更新業主
DELETE /api/clients/{id}       // 刪除業主

// 聯繫方式管理
GET /api/clients/{id}/contacts     // 業主聯繫方式
POST /api/clients/{id}/contacts    // 新增聯繫方式
PUT /api/contacts/{id}             // 更新聯繫方式
DELETE /api/contacts/{id}          // 刪除聯繫方式

// 數據匯出
GET /api/export/projects?format=excel&filters={}
GET /api/export/clients?format=csv
GET /api/export/reports?type=revenue&period=monthly
```

### API 回應格式標準化

```json
{
    "success": true,
    "data": {...},
    "message": "操作成功",
    "meta": {
        "current_page": 1,
        "total": 100,
        "per_page": 15
    }
}
```

## 4. 前端功能規劃建議

### 頁面組件規劃

```javascript
// 新增專案管理相關的 stores
stores/
├── projects.js      // 專案狀態管理
├── clients.js       // 業主狀態管理
└── dashboard.js     // 儀表板統計數據

// 新增頁面組件
pages/
├── projects/
│   ├── index.vue           // 專案列表
│   ├── create.vue         // 新增專案
│   ├── [id]/
│   │   ├── index.vue      // 專案詳情
│   │   └── edit.vue       // 編輯專案
├── clients/
│   ├── index.vue          // 業主列表
│   ├── create.vue         // 新增業主
│   └── [id]/
│       ├── index.vue      // 業主詳情
│       └── edit.vue       // 編輯業主
└── dashboard/
    └── index.vue          // 更新儀表板
```

### 重要功能組件

```vue
<!-- 專案狀態管理組件 -->
<ProjectStatusBadge />
<ProjectProgressBar />
<RevenueChart />
<ProjectTimeline />

<!-- 業主管理組件 -->
<ContactMethodsManager />
<ClientProjectsList />

<!-- 儀表板組件 -->
<StatisticsCards />
<RevenueChart />
<ProjectStatusChart />
<RecentActivities />
```

## 5. 專案開發流程建議

### 階段一：環境建置（1週）
1. 設定 Laravel 後端環境與 Docker
2. 建立資料庫結構與 migrations
3. 設定 API 基礎架構
4. 前端整合現有模板

### 階段二：核心功能開發（3週）
1. 業主管理功能（CRUD + 聯繫方式管理）
2. 專案管理功能（CRUD + 狀態管理）
3. API 整合與資料驗證
4. 前端表單與列表頁面

### 階段三：進階功能（2週）
1. 儀表板統計功能
2. 搜尋、篩選、排序功能
3. 資料匯出功能
4. 響應式設計優化

### 階段四：測試與部署（1週）
1. 功能測試與 Bug 修復
2. 效能優化
3. 部署配置
4. 使用者文件

## 6. 技術選型建議

### 前端技術棧（已優化）
- **框架**：Nuxt 3（已有）
- **狀態管理**：Pinia（已有）
- **UI 框架**：Nuxt UI + Tailwind CSS（已有）
- **圖表庫**：Chart.js（推薦，輕量且功能完整）
- **日期處理**：Day.js（輕量替代 Moment.js）
- **表單驗證**：VeeValidate（與 Vue 3 整合更好）
- **HTTP 客戶端**：$fetch（Nuxt 內建）
- **數據匯出**：SheetJS（Excel 匯出）
- **通知組件**：Nuxt UI Notifications

### 後端技術棧（已優化）
- **框架**：Laravel 11（最新 LTS 版本）
- **資料庫**：MySQL 8.0
- **認證**：Laravel Sanctum
- **API 查詢**：Spatie Laravel Query Builder
- **資料匯出**：Laravel Excel (Maatwebsite)
- **工作隊列**：Redis（用於統計數據計算）
- **容器化**：Docker + Docker Compose
- **API 文件**：Laravel API Documentation Generator

### 開發工具與依賴
```json
{
  "前端依賴": {
    "chart.js": "^4.4.0",
    "dayjs": "^1.11.10",
    "vee-validate": "^4.12.0",
    "@vee-validate/rules": "^4.12.0",
    "xlsx": "^0.18.5"
  },
  "後端依賴": {
    "laravel/sanctum": "^3.3",
    "spatie/laravel-query-builder": "^5.7",
    "maatwebsite/excel": "^3.1",
    "predis/predis": "^2.2"
  },
  "開發工具": {
    "前端": ["ESLint", "Prettier", "Vue DevTools"],
    "後端": ["Laravel Telescope", "Clockwork", "Laravel Debugbar"]
  }
}
```

## 7. 安全性考量建議

### API 安全性
```php
// Laravel 中介軟體配置
'api' => [
    'throttle:api',        // API 限制
    'auth:sanctum',        // 認證檢查
    'cors',                // CORS 設定
],

// 資料驗證範例
class ProjectRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|in:website,script,server,custom',
            'client_id' => 'required|exists:clients,id'
        ];
    }
}
```

### 前端安全性
```javascript
// API 請求攔截器
$fetch.create({
  baseURL: '/api',
  onRequest({ request, options }) {
    // 添加認證 token
    options.headers = {
      ...options.headers,
      Authorization: `Bearer ${useAuthStore().token}`
    }
  },
  onResponseError({ response }) {
    // 統一錯誤處理
    if (response.status === 401) {
      navigateTo('/auth/login')
    }
  }
})
```

### Docker 安全配置
```yaml
# docker-compose.yml
version: '3.8'
services:
  app:
    build: .
    environment:
      - APP_ENV=production
      - DB_CONNECTION=mysql
    networks:
      - backend
    volumes:
      - ./storage:/var/www/storage:rw
  
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - backend

networks:
  backend:
    driver: bridge

volumes:
  mysql_data:
```

## 總結建議

1. **優先級排序**：建議按照業主管理 → 專案管理 → 儀表板的順序開發
2. **資料完整性**：重點關注專案狀態的流程管理和金額計算的準確性
3. **使用者體驗**：利用現有模板的響應式設計，確保在各種裝置上都有良好體驗
4. **可擴展性**：預留接口供未來可能的功能擴展（如發票管理、時間追蹤等）

這個架構設計考慮了您的具體需求，同時充分利用了現有的前端模板基礎。建議先從核心的 CRUD 功能開始實作，再逐步加入統計和進階功能。

## 下一步行動建議

### 立即執行項目

1. **環境建置**
   - 設定 Laravel 11 後端環境與 Docker 配置
   - 建立資料庫結構與 migrations
   - 設定 API 基礎架構與認證系統
   - 前端整合現有 Nuxt 3 模板

2. **核心開發順序**
   - **第一階段**：用戶認證 + 業主管理（CRUD + 聯繫方式）
   - **第二階段**：專案管理（CRUD + 狀態管理）
   - **第三階段**：儀表板統計功能（圖表 + 時間統計）
   - **第四階段**：進階功能（搜尋、篩選、匯出）

3. **技術實作重點**
   - 資料庫索引優化（針對大量數據查詢）
   - API 分頁機制（支援大量專案數據）
   - 統計數據緩存（提升儀表板載入速度）
   - 響應式設計確保各裝置相容性

### 關鍵成功因素

- **資料完整性**：專案狀態流程管理和金額計算準確性
- **效能優化**：支援大量數據的查詢和統計功能
- **使用者體驗**：充分利用現有模板的設計優勢
- **可擴展性**：預留多用戶支援和功能擴展接口

### 風險控制

- **資料備份機制**：定期自動備份重要業務數據
- **API 限流**：防止異常請求影響系統效能
- **錯誤監控**：建立完善的錯誤追蹤和通知機制
- **安全性檢查**：定期進行安全漏洞掃描和更新

這個更新後的架構設計完全符合您確認的需求，特別針對時間統計、彈性設計和多用戶支援進行了優化。建議按照上述階段順序進行開發，確保每個階段都經過充分測試後再進入下一階段。