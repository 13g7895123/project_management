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