1. 這是一個接案的專案管理網站，僅有後台的形式，側邊欄幫我包含以下項目與功能
    - 儀錶板，顯示統計的各項資訊
    - 專案管理
        * 名稱
        * 描述
        * 類別，網站、腳本、伺服器、自訂
        * 金額
        * 接洽日期
        * 開始執行日期
        * 完成日期
        * 收款日期
    - 業主管理
        * 稱呼
        * 聯繫方式
            + 用Key => value的方式，可能有多種
        * 認識方式
2. 幫我串這個git，https://github.com/13g7895123/project_management.git
3. 前端已有基礎的後台模板，請用這個模板進行功能調整
4. 後端幫我用Laravel開發，並存入backend這個資料夾，並且後端需要有獨立的docker-compose可以啟用後端專案
5. 請幫我確認一下，我後端獨立部屬到vps上面他卻出現symfony，請確認是否有問題
6. 請幫我確認後端的所有對外PORT是否都有參找後端資料夾中的.env配置
7. 幫我調整成對外PORT在最上面可以一次編輯
8. 請確保下面一樣的PORT有吃到最上面的設定
9. 我目前直接使用backend中的docker設定，啟用後雖然容器可以使用，但是網站卻無法使用，請幫我重新確認
10. 目前後端專案好了，幫我確保PORT有吃到.env最上面的設定
11. 我把這個專案clone到vps上面，但app無法啟用，似乎是沒有composer-install的緣故，幫我修復
12. 可以幫我後端的部份加上nginx嗎，完成後add commit push，記得不要加上「Generated with Claude Code」的標記
13. 我在vps上面使用後，nginx顯示502，請幫我確認直到後端可以正常執行，完成後add commit push，記得不要加上「Generated with Claude Code」的標記
14. 幫我分別整理frontend與backend的根目錄，希望專案可以放在src資料夾中，其餘非專案項目保留在根目錄，並同時更新有使用到的Dockerfile與docker-compose.yml，確保可以正常運行，完成後add commit push，記得不要加上「Generated with Claude Code」的標記
15. 改完後端出現這個錯誤"No application encryption key has been specified."，完成後add commit push，記得不要加上「Generated with Claude Code」的標記
16. 幫我為這個前端寫一個可以執行的docker-compose檔案，完成後add commit push，記得不要加上「Generated with Claude Code」的標記
17. 請幫我把前端所有使用到的api，整理進composables中統一管理
18. 後端路徑請直接使用/api即可
19. /projects/create這個頁面的提交並沒有打到API並更新資料庫，請幫我讀取backend資料夾的後端專案後完善他，可以請frontend agent處理
20. add commit push，記得不要加上「Generated with Claude Code」的標記
21. 我目前推上去後在vps上面沒有看到有更新的功能，會建議怎麼執行測試呢
22. 幫我在這個表單旁邊補上一個填入假資料的功能，然後執行第20點
23. 前端使用的一堆api都顯示404，請backend agennt與frontend agent溝通一下，確認是哪邊的問題，並修正完全，目前先以/projects/create這個頁面用到的先處理，完成後執行第20點
24. 幫我同步確認其他頁面有沒有這個問題，完成後執行第20點
25. 幫我請uiux agent重新設計一下登入頁面，目前的好醜，完成後執行第20點
26. 我在/client裡面出現這個錯誤，"Cannot read properties of null (reading 'id')"，其他頁面也有，幫我修復一下，完成後執行第20點
27. 我在/client/create終點新增聯繫方式沒有反應，另外我希望一進入網站可以是亮色主題的
28. 幫我加入api的基礎路由為https://project.mercylife.cc/api/，完成後執行第20點
29. 請確保最後的路由是第28點的資訊，我目前看到的正式環境還是吃docker環境的domain，完成後執行第20點
30. 後端打了這支api "https://project.mercylife.cc/api/clients"，但她卻報了這個錯誤 "The route clients could not be found."，請後端agent幫我修復他，後端domain與前端是一樣的，只有/api proxy後端服務，完成後執行第20點
31. 我現在看mysql資料庫都是空的，完全沒有資料表，可以在建置的時候，幫我確認是否要更新資料表嗎，透過laravel的command方式，完成後執行第20點
32. 我在後端沒有看到/clients這支api，前端卻呼叫了，後端是laravel專案，請幫我重新確認並修復，包含該專案的所有api，請backend agent與frontend agent溝通並解決這個問題，完成後執行第20點
33. 後端改完後，麻煩frontend agent確認前端是否都有使用對應的api，如果有缺請後端補上，完成後執行第20點
34. 請幫我重新確認一次，我後端連/client都沒看到，到底是在檢查個屁，請frontend-ui-specialist agent與backend-architecture-reviewer agent重新確認一次，完成後執行第20點
35. 完成以下功能
    - 請frontend-ui-specialist agent實作/profile這個頁面沒有功能
    - 請uiux agent設計404的頁面，並請frontend-ui-specialist agent實作
    - 請frontend-ui-specialist agent確認主題設定的顏色應用後沒有反應
    - 執行第20點
36. 完成以下功能
    - 後臺請加入身分驗證，沒有登入的不能使用
    - 執行第20點
37. 完成以下功能
    - 請重新確認middleware，admin與auth的內容完全一樣
    - 使用者包含管理員的資料，請移除所有fake data，並建立真實資料進資料庫
    - 目前的user都沒有資料，且後台也沒有可以管理的地方，幫我補上，包含前端與後端
    - 註冊的部分也是完全沒有寫入資料庫，幫我補上，包含前端與後端
    - 執行第20點
38. 前端調整完出現以下錯誤，"[nuxi]  ERROR  Nuxt Build Error: [nuxt:pages-macros-transform] Multiple definePageMeta calls are not supported. File: /app/pages/profile.vue
9.140 file: /app/pages/profile.vue?macro=true"
39. 如果我想整合這個專案前後端的docker-compose，幫我讀取完整專案後給我意見與執行項目，寫入TODO.md
40. 移除階段二的部分，這個部分已經由其他工具處理
41. 目前使用者帳號我還是沒有看到出現在資料庫中，現在完全無法登入使用，幫我排除這個問題，且是用資料來自資料庫的方式
42. 目前側邊欄沒有管理員管理與使用者管理的路徑，幫我確認是否有這些功能並補上，完成後執行第20點
43. 請提供一個管理員帳號給我，希望這個帳號是有寫在資料庫中的，不然我無法使用，完成後執行第20點
44. 針對TODO.md，調整專案的docker-compose結構，希望調整前後不會影響功能
45. 目前登入頁打的API是這支http://backend:8000/api/auth/login，我希望他可以打正確的domain到https://project.mercylife.cc/這支，環境配置都完成了
46. 他現在執行正確的api路徑了，但response的msg顯示Server Error，幫我確認是甚麼問題
47. CD報了這個錯誤，可以幫我檢查是否有那裡錯誤嗎"the input device is not a TTY
Error: Process completed with exit code 1."
48. API似乎有問題，以下是他回傳的資訊
{
    "success": false,
    "message": "Invalid credentials",
    "debug": {
        "login_type": "username",
        "login_value": "admin",
        "user_exists": true,
        "total_users": 6
    }
}
49. 幫我修正用戶管理的列表，都沒有看到任何從後端來的資料，另外欄位名稱幫我調整成大一點的，列表需要有分頁功能
50. 完成以下功能
    - 業主列表沒有正確顯示讀取到的資料
    - 新增專案中的業主下拉選單也沒有讀取到正確資料
51. 初步排查應該是因為v-if="client"，可以幫我印出filteredClients來確認是否是資料讀取的問題嗎
52. 經過DEBUG模式確認是有取到資料的，但datatable卻沒有顯示任何資料，幫我確認一下
53. 完成以下功能
    - 業主列表移除DEBUG用的功能
    - 編輯無法使用
    - 專案列表的datatable一樣沒有顯示任何資料
54. 專案列表的datatable顯示這個錯誤，載入專案資料失敗：格式錯誤，請確認API格式後修改
55. 資料庫中有存在資料，但API沒有帶資料過來，幫我確認一下API是否有問題
56. 修正用戶管理裡面沒有拿到資料的問題，目前看起來API回傳500應該是有問題，幫我修正他
57. 用戶管理出現的錯誤訊息，"'Class "Spatie\\QueryBuilder\\QueryBuilder" not found'"
58. 這隻API出現point 57的錯誤，另外本地沒有docker架這個專案的環境，修正後不用測試，/api/users?page=1&per_page=15&sort=-created_at
59. 我給你完整路徑吧，https://project.mercylife.cc/api/users?page=1&per_page=15&sort=-created_at，請登入後打這支API，修復到她好為止，更新可以直接push上去，等CD結束他就會套用了，用這樣慢慢除錯吧
60. API已可正常使用，但目前用戶管理列表中顯示的資料為假資料，請幫我改為由API獲取的資料
61. 部屬報了以下錯誤，請修正
 > [backend stage-0 10/25] RUN composer dump-autoload --no-dev --optimize:
0.387 Generating optimized autoload files
2.440 > Illuminate\Foundation\ComposerScripts::postAutoloadDump
2.455 > @php artisan package:discover --ansi
2.589
2.597 In ProviderRepository.php line 206:
2.598
2.598   Class "Laravel\Sail\SailServiceProvider" not found
2.598
2.598
2.606 Script @php artisan package:discover --ansi handling the post-autoload-dump event returned with error code 1
------
WARNING: The "PUSHER_PORT" variable is not set. Defaulting to a blank string.

Dockerfile:44

--------------------

  42 |

  43 |     # Complete composer setup

  44 | >>> RUN composer dump-autoload --no-dev --optimize

  45 |

  46 |     # Copy .env.example to .env if .env doesn't exist

--------------------

target backend: failed to solve: process "/bin/sh -c composer dump-autoload --no-dev --optimize" did not complete successfully: exit code: 1
62. 目前專案列表中的datatable的API來源，data依舊是空的，請確認API端是否有出現邏輯錯誤，admin應該要可以看到所有的專案
63. 右側儀錶板點下去沒有反應，幫我修復這個功能，完成後執行point 20
64. 專案列表拿到的data依舊是空的，請重新確認，側邊欄的儀表板點下去也還是沒有反映
65. 專案列表中還是沒有資料，請確認API是否有正確拿到資料庫的資料，目前資料庫確認是有資料的API路徑如下https://project.mercylife.cc/api/projects?search=&category=&status=
66. 我看你的SQL最後是這樣select * from `projects` where `status` is null and `category` is null and (`name` like "%%" or `description` like "%%") order by `created_at` desc; 但根本就取不到任何資料，請幫我修正
67. 專案的編輯按鈕目前沒有任何作用，幫我修正
68. 完成以下功能 
    - 目前儀錶板的資料似乎沒有連動到專案的資料，幫我更新實作
    - 完成收入趨勢與專案動態
    - 專案設定加入預計完成日的欄位，並需要加總預計的收入到儀表板中
69. 完成以下功能 
    - 幫我把當前完成的功能全部加上測試，前後端都要
    - 幫我把測試加入自動部屬中，並寫一份docs\CI.md文件給我
    - 前面似乎有看到PHP版本過舊的問題，幫我升級，並利用測試確保功能在升級後都可以正常運行，有錯誤請進行修正
    - 側邊欄的收折，如果把側邊欄收起來，右側的navbar與內容會有跑版的現象，請frontend agent協助處理，
    - 執行 point 20
70. 完成以下功能
    - 業主管理頁面，各業主的查看專案點下去沒有反應
    - 側邊欄的收折，如果把側邊欄收起來，右側的navbar與內容會有跑版的現象，請frontend agent協助處理，
    - 執行 point 20
71. 完成以下功能
    - 編輯專案請用新增專案那個頁面就好，不要用彈窗顯示
    - 儀表板的收入趨勢並沒有圖表顯示
    - 專案動態也是一片空白
    - 執行 point 20
72. 完成以下功能
    - 請逐項確認測試功能的CI部分，目前部屬CI部分有跑但是會失敗
    - 執行 point 20
73. 完成以下功能
    -幫我移除seed中的測試專案，用戶管理也保留system admin就好，其餘通通刪除，登入頁面也不要提示帳號資訊了
    - 執行 point 20
74. 完成以下功能
    - 登入頁請幫我把兩個區塊底下的框框合在一起
    - 側邊欄的登出按鈕沒有反應
    - 一登入請不要導到dashboard/analytics，到根目錄即可
    - 收入趨勢目前還是沒有看到相關圖表
    - seed是不是刪除後需要執行甚麼動作，我看部屬後原先的測試專案還是會出現，用戶也是
    - 執行 point 20
75. 完成以下功能
    - 修改密碼的功能API method有問題
    - 收入趨勢目前還是沒有看到相關圖表
    - 執行 point 20
76. 完成以下功能
    - 登入後請不要導到dashboard/analytics，到根目錄即可，檢查所有前端功能，不要有導到dashboard/analytics的地方，並刪除該檔案
    - 根目錄index.vue中，有看到收入趨勢的code，但沒有圖片顯示，幫我進一步除錯
    - 執行 point 20
77. 完成以下功能
    - 76的第二點報了錯誤，"No revenue data found, using fallback data for demo"，"Chart canvas not available"
    - 用戶資料幫我保留目前正常使用的資料即可，其餘的幫我從SEED全部刪除，我不要部屬後又有那些資料，請用與專案一樣的方式移除
    - 執行 point 20
78. 完成以下功能
    - 目前有出現圖表了，但上面的資料與API資料並沒有匹配
    - 用戶資料幫我保留帳號為admin，權限為管理員那一筆即可，其餘的通通移除，包含SEED，不要部屬後又出現
    - 另外，儀表板的預期總收入公式幫我確認一下，為當前未完成的所有收入總合，潛在收入則為進行中的專案，我目前看到洽談中的也被計算進去了
    - 移除註冊功能
    - 執行 point 20
79. 完成以下功能
    - 收入趨勢幫我多加入一條預期收入的資料
    - 執行 point 20