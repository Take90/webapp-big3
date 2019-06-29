<?php
require('function.php');
debug('================================');
debug('パスワード再発行メール送信ページ');
debug('================================');
debugLogStart();

if(!empty($_POST)){
    debug('POST送信あり');
    debug('POST情報：'.print_r($_POST,true));
    $email = $_POST['email'];

    validRequired($email,'email');
    if(empty($err_msg)){
        debug('未入力チェックOK');
        validEmail($email,'email');
        validMaxLen($email,'email');

        if(empty($err_msg)){
            debug('バリデーションOK');
            try{
                $dbh = dbConnect();
                $sql = 'SELECT count(*) FROM users WHERE email=:email AND delete_flg=0';
                $data = array(':email'=>$email);
                $stmt = queryPost($dbh,$sql,$data);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if($stmt && array_shift($result)){
                    debug('クエリ成功、DB登録あり');
                    $_SESSION['msg_success'] = SUC02;
                    $auth_key = makeRandKey();
                    //メール送信
                    $from = 'hello@wip.tokyo';
                    $to = $email;
                    $subject = 'パスワード再発行認証';
                    $comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：https://big3.wip.tokyo/passRemindReceive.php
認証キー：{$auth_key}
※認証キーの有効期限は30分となります

認証キーを再発行されたい場合は下記ページより再度再発行をお願い致します。
https://big3.wip.tokyo/passRemindSend.php

////////////////////////////////////////
カスタマーセンター
URL https://wip.tokyo/
E-mail hello@wip.tokyo
////////////////////////////////////////
EOT;
                    sendMail($from,$to,$subject,$comment);
                    $_SESSION['auth_key'] = $auth_key;
                    $_SESSION['auth_email'] = $email;
                    $_SESSION['auth_key_limit'] = time()+(60*30);
                    debug('セッション変数の中身：'.print_r($_SESSION,true));
                    header("Location:passRemindReceive.php");
                }else{
                    debug('クエリ失敗か、DBに登録のないメールアドレスが入力された');
                    $err_msg['common'] = MSG07;
                }
            }catch(Exception $e){
                error_log('エラー発生：'.$e->getMessage());
                $err_msg['common'] = MSG07;
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
                    <p>メールアドレスを入力してください。認証コードを含むメールが送信されます。</p>
                    <div class="area-msg">
                        <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                        <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
                    </div>
                    <label>
                    <input type="text" name="email">
                    </label>
                    <input type="submit" value="送信">
                </div>
            </form>
        </div>



    </main>
    <?php require('footer.php'); ?>