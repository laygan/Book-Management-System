# Book Management System

PHPを用いたWebアプリケーションです（現状）
データベースと連携しています。
連携データベースはPostgreSQLです。
対応のインタフェースを作れば他のデータベースシステムでの稼働も可能になります。

## 動作させる前に
このアプリケーションはデータベースと連携しています。
データベーステーブルは全部で３つあります。
* bookshelf：本棚。ローカルデータベース上に本の情報を格納するのに使います。
* borrows：貸出情報。このテーブルにて貸出情報を記録します。
* br_user：貸出を受ける人のユーザ情報を格納するテーブルです。

データベースユーザー名は_postgres_となっています。

## 初期画面での有効コマンド
初期画面で管理メニューと連続登録モードに移行させることができます。
### 管理メニュー
初期画面のISBNコード入力ボックスに**admin**と入力して送信します。

ユーザーの追加や削除、貸出中の書籍一覧や履歴を表示させることができます。
（本棚のデータ修正も可能になる予定です）
### 連続登録モード
初期画面のISBNコード入力ボックスに**fast**と入力して送信します。

連続登録モードでは、バーコードリーダー等を利用して高速に書籍をデータベースに登録することができます。
画面が自動推移するので、コードの入力だけでページの送り戻しは不要です。
ただし、同じ本を登録した場合や本の情報を特定できなかった場合は登録作業が中断されるので、戻る操作が必要です。

# 最後に
初期設定が面倒です、そのうち設定用スクリプトを書くと思います。
また、時間を記録するので、マシンの時刻とタイムゾーンはしっかりと設定されている必要があります。
