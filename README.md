# ポートフォリオとして制作したオリジナルウェブアプリ

## 目的
学習したことのアウトプットとして実際にウェブアプリケーションを作ること。特にデータベースを使ったデータの保存などの理解を深めること。

## 制作工程及び説明
- どんなアプリにするか  
趣味の筋トレの記録をつけられるアプリに。

- 基本機能洗い出し
  - ユーザー登録
  - ログイン
  - ログアウト
  - 退会
  - 投稿
  - 投稿一覧
  - 投稿編集
  - 投稿削除
  - パスワード変更
  - パスワードリマインダー

- ページ構成検討
  - ユーザー登録ページ
  - ログインページ
  - トップページ
  - 最大挙上重量表示ページ
  - パスワード変更ページ
  - パスワードリマインダー送信ページ
  - パスワードリマインダー入力ページ
  - 退会ページ

- テーブル設計
  - usersテーブル
    - id
    - email
    - password
    - create_at
    - login_time
    - modified
    - delete_flg
  - postテーブル
    - id
    - item_name
    - weight
    - total_rep
    - total_weight
    - user_id
    - create_at
    - modified
    - delete_flg

- ワイヤーフレーム作成  
スピードを考慮して手書きで作成。

- コーディング(HTML+CSS+jQ)  
フルスクラッチで制作。このアプリは筋トレの合間に記録するという状況を想定していたため、スマホファーストでのデザインを考慮した。  もしPCから閲覧したとしても横幅が大きくなりすぎないようにwidthを800pxまでと制限している。

- 実装  
基本機能であげた機能をphpで実装。フレームワークを使って作ることが普通かと思うが、処理内容を理解しておくことは重要だと思いフルスクラッチで実装した。

- テスト  
ページレイアウトの確認、パスワードリマインダーの動作確認をした。

- 公開  
MAMPを使ってローカルで開発していたものをサーバーへアップロード。

## 気づきなど
- コーディングをしながらページ構成の変更を数回することになった。全てのベースとなる設計をもっと入念にやる必要がある。
- 処理内容のログの出力ファイルを指定し、そこに記録させながら実装を進めたので、速やかなエラーの特定と対処ができた。
- SQL文で、SELECT/INSERT/UPDATEそれぞれの使い分けや、WHEREなどを使った書き方について理解が深まった。  SELECTでdelete_flgの条件を書き忘れてしまい、削除したはずのものが表示されることがあったのですが、その点は理解が深まった。
- 各種バリデーションが、どのタイミングで必要になるかイメージをつかめた。
- W3Cの規格を守ったHTMLの構造にすべき。
- 公開までの制作期間は10日程度だったが、公開後にパスワードリマインダーで送られるメールに誤植があったりとミスを見つけて直すことがあった。

##　今後の方針
今回作ったものを土台にして、実際に使えるサービスを作る。そのためには、テーブル設計を見直し正規化をする、ある期間ごとのデータの集計をし、それをグラフ化したりといったことが出来るようする。
(php)フレームワーク(FuelPHP,Lalavel)を使えるようにする。

リンク：https://big3.wip.tokyo
