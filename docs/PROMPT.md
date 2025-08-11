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