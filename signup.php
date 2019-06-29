<?php
require('function.php');
debug('================================');
debug('ユーザー登録');
debug('================================');
debugLogStart();

if(!empty($_POST)){
    //変数にユーザー情報代入
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_retype = $_POST['password_retype'];

    //各フォームの未入力チェック
    validRequired($email,'email');
    validRequired($password,'password');
    validRequired($password_retype,'password_retype');

    if(empty($err_msg)){
        //メールアドレス
        //形式チェック
        validEmail($email,'email');
        //重複チェック
        validEmailDup($email);
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
            //パスワード再入力
            //同値チェック
            validMatch($password,$password_retype,'password_retype');

            if(empty($err_msg)){
                //DBへ接続開始
                try{
                    $dbh = dbConnect();
                    $sql = 'INSERT INTO users (email,password,create_at,login_time) VALUES(:email,:password,:create_date,:login_time)';
                    $data = array(':email'=>$email,':password'=>password_hash($password,PASSWORD_DEFAULT),':create_date'=>date('Y-m-d H:i:s'),':login_time'=>date('Y-m-d H:i:s'));
                    $stmt = queryPost($dbh,$sql,$data);

                    if($stmt){
                        //ログイン有効期限
                        $sesLimit = 60*60;
                        //最終ログイン日時を現在に
                        $_SESSION['login_date'] = time();
                        $_SESSION['login_limit'] = $sesLimit;
                        //ユーザーIDの格納
                        $_SESSION['user_id'] = $dbh->lastInsertId();

                        debug('セッション変数の中身：'.print_r($_SESSION,true));
                        header("Location:index.php");
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
            <a href="https://workout.wip.tokyo">BIG3 WorkoutLog</a>
        </h1>

        <div class="menu-trigger js-toggle-sp-menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <nav class="top-nav js-toggle-sp-menu-target">
            <ul class="menu">
                <li class="menu-item"><a href="login.php" class="menu-link">ログイン</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <form class="form" method="post" action="">
                <h2 class="title">ユーザー登録</h2>
                <div class="area-msg">
                    <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                </div>
                <div class="form-body">
                <label>
                    メールアドレス
                    <div class="area-msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></div>
                    <input type="email" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>" autocomplete="off">
                </label>
                <label>
                    パスワード(半角英数字6文字以上)
                    <div class="area-msg"><?php if(!empty($err_msg['password'])) echo $err_msg['password']; ?></div>
                    <input type="password" name="password" value="<?php if(!empty($_POST['password'])) echo $_POST['password']; ?>">
                </label>
                <label>
                    パスワード再入力<div class="area-msg"><?php if(!empty($err_msg['password_retype'])) echo $err_msg['password_retype']; ?></div>
                    <input type="password" name="password_retype"　value="<?php if(!empty($_POST['password_retype'])) echo $_POST['password_retype']; ?>" autocomplete="off">
                </label>
                <input type="submit" value="登録">
            </div>
            </form>
        </div>


    </main>
    <?php require('footer.php'); ?>