<?php
require('function.php');
debug('================================');
debug('パスワード再発行認証キー入力ページ');
debug('================================');
debugLogStart();

//セッションに認証キーがあるか確認
if(empty($_SESSION['auth_key'])){
    header("Location:passRemindSend.php");
}

if(!empty($_POST)){
    debug('POST送信あり');
    debug('POST情報'.print_r($_POST,true));

    $auth_key = $_POST['token'];
    validRequired($auth_key,'token');

    if(empty($err_msg)){
        debug('未入力チェックOK');
        validLength($auth_key,'token');
        validHalf($auth_key,'token');

        if(empty($err_msg)){
            debug('バリデーションOK');

            if($auth_key !== $_SESSION['auth_key']){
                $err_msg['common'] = MSG13;
            }
            if(time() > $_SESSION['auth_key_limit']){
                $err_msg['common'] = MSG14;
            }

            if(empty($err_msg)){
                debug('認証OK');

            $pass = makeRandKey();
            try{
                $dbh = dbConnect();
                $sql = 'UPDATE users SET password=:password WHERE email=:email AND delete_flg=0';
                $data = array(':password'=>password_hash($pass,PASSWORD_DEFAULT), ':email'=>$_SESSION['auth_email']);
                $stmt = queryPost($dbh,$sql,$data);

                if($stmt){
                    debug('クエリ成功');
                    //メール送信
                    $from = 'hello@wip.tokyo';
                    $to = $_SESSION['auth_email'];
                    $subject = 'パスワード再発行完了';
                    $comment = <<<EOT
本メールアドレス宛にパスワードの再発行を致しました。
下記のURLにて再発行パスワードをご入力頂き、ログインください。

ログインページ：https://big3.wip.tokyo/login.php
再発行パスワード：{$pass}
※ログイン後、パスワードのご変更をお願い致します。

////////////////////////////////////////
カスタマーセンター
URL  https://big3.wip.tokyo/
E-mail hello@wip.tokyo
////////////////////////////////////////
EOT;
                    sendMail($from,$to,$subject,$comment);
                    session_unset();
                    $_SESSION['msg_success'] = SUC02;
                    debug('セッション変数の中身：'.print_r($_SESSION,true));
                    header("Location:login.php");
                }else{
                    debug('クエリ失敗');
                    $err_msg['common'] = MSG07;
                }
            }catch(Exception $e){
                error_log('エラー発生：'.$e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
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
                <li class="menu-item"><a href="signup.php" class="menu-link">新規登録</a></li>
                <li class="menu-item"><a href="login.php" class="menu-link">ログイン</a></li>
            </ul>
        </nav>
</header>

    <main>

        <div class="form-container">
            <form class="form" method="post" action="">
                <h2 class="title">パスワードリマインダー</h2>
                <div class="form-body">
                    <p>ご指定のメールアドレスにお送りした認証キーを入力してください。</p>
                    <div class="area-msg">
                        <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                    </div>
                    <label>
                        <div class="area-msg">
                            <?php if(!empty($err_msg['token'])) echo $err_msg['token']; ?>
                        </div>
                    <input type="text" name="token" value='<?php getFormData('token'); ?>'>
                    </label>
                    <input type="submit" value="再発行">
                </div>
            </form>
        </div>
    </main>
    <?php require('footer.php'); ?>