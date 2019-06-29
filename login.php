<?php
require('function.php');
debug('================================');
debug('ログインページ');
debug('================================');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// ログイン画面処理
//================================
if(!empty($_POST)){
    debug('POST送信あり');
    //変数にユーザー情報代入
    $email = $_POST['email'];
    $password = $_POST['password'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    //各フォームの未入力チェック
    validRequired($email,'email');
    validRequired($password,'password');

    //メールアドレス
    //形式チェック
    validEmail($email,'email');
    //最大文字数チェック
    validMaxLen($email,'email');

    //パスワード
    //半角英数字チェック
    validHalf($password,'password');
    //最小文字数チェック
    validMinLen($password,'password');
    //最大文字数チェック
    validMaxLen($password,'password');

    if(empty($err_msg)){
        debug('バリデーションOK');
        //DB接続開始
        try{
            $dbh = dbConnect();
            $sql = 'SELECT password,id FROM users WHERE email=:email AND delete_flg=0';
            $data = array(':email'=>$email);
            $stmt = queryPost($dbh,$sql,$data);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            debug('クエリ結果の中身：'.print_r($result,true));

            //パスワード照合
            if(!empty($result) && password_verify($password,array_shift($result))){
                debug('パスワードが一致しました');
                //ログイン有効期限
                $sesLimit = 60*60;
                //最終ログイン日時を現在日時に更新
                $_SESSION['login_date'] = time();

                //ログイン保持にチェックが有るとき
                if($pass_save){
                    debug('ログイン保持にチェックあり');
                    $_SESSION['login_limit'] = $sesLimit*24*30;
                }else{
                    debug('ログイン保持にチェックなし');
                    $_SESSION['login_limit'] = $sesLimit;
                }
                //ユーザーIDを格納
                $_SESSION['user_id'] = $result['id'];
                debug('セッション変数の中身：'.print_r($_SESSION,true));
                debug('マイページへ遷移');
                header("Location:index.php");
            }else{
                debug('パスワードが一致しません');
                $err_msg['common'] = MSG09;
            }
        }catch(Exception $e){
            error_log('エラー発生:'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php require('head.php'); ?>
<body>
<header class="header">
        <h1>
            <a href="https://big3.wip.tokyo">BIG3 WorkoutLog</a>
        </h1>

        <div class="menu-trigger js-toggle-sp-menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <nav class="top-nav js-toggle-sp-menu-target">
            <ul class="menu">
                <li class="menu-item"><a href="signup.php" class="menu-link">ユーザー登録</a></li>
            </ul>
        </nav>
    </header>

    <main>

        <div class="form-container">
            <form class="form" method="post" action="">
                <h2 class="title">ログイン</h2>
                <div class="area-msg">
                    <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                </div>
                <div class="form-body">
                    <label>
                    メールアドレス(テスト用：test@test.test)
                    <div class="area-msg">
                        <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
                    </div>
                    <input type="email" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>" autocomplete="off">
                    </label>
                    <label>
                    パスワード(テスト用：testtest)
                    <div class="area-msg">
                        <?php if(!empty($err_msg['password'])) echo $err_msg['password']; ?>
                    </div>
                    <input type="password" name="password" value="<?php if(!empty($_POST['password'])) echo $_POST['password']; ?>" autocomplete="off">
                    </label>
                    <label class="pass_save">
                    <input type="checkbox" name="pass_save">次回ログインを省略する
                    </label>
                    <input type="submit" value="ログイン">
                    <div class="msg-container">
                        <a href="passRemindSend.php">パスワードを忘れた方</a>
                    </div>
                </div>
            </form>
        </div>



    </main>
    <?php require('footer.php'); ?>