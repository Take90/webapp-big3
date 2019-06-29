<?php
require('function.php');
debug('================================');
debug('パスワード変更ページ');
debug('================================');
debugLogStart();

//データベースからユーザー情報を取得
$userData = getUser($_SESSION['user_id']);
debug('ユーザー情報：'.print_r($userData,true));

if(!empty($_POST)){
    debug('POST送信あり');
    debug('POST情報：'.print_r($_POST,true));

    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $new_password_retype = $_POST['new_password_retype'];

    validRequired($old_password,'old_password');
    validRequired($new_password,'new_password');
    validRequired($new_password_retype,'new_password_retype');

    if(empty($err_msg)){
        debug('未入力チェックOK');
        validPass($new_password,'new_password');
        validPass($old_password,'old_password');

        if(!password_verify($old_password,$userData['password'])){
            $err_msg['old_password'] = MSG10;
        }

        if($old_password === $new_password){
            $err_msg['new_password'] = MSG11;
        }

        validMatch($new_password,$new_password_retype,'new_password_retype');

        if(empty($err_msg)){
            debug('バリデーションOK');
            try{
                $dbh = dbConnect();
                $sql = 'UPDATE users SET password=:password WHERE id=:id';
                $data = array(':password'=>password_hash($new_password,PASSWORD_DEFAULT), ':id'=>$_SESSION['user_id']);
                $stmt = queryPost($dbh,$sql,$data);

                if($stmt){
                    $_SESSION['msg_success'] = SUC01;
                    //メール送信
                    $username = ($userData['email']) ? $userData['email'] : 'anonymous';
                    $from = 'hello@wip.tokyo';
                    $to = $userData['email'];
                    $subject = 'パスワード変更通知';
                    $comment = <<<EOT
{$username}さん
パスワードが変更されました。

////////////////////////////////////////
カスタマーセンター
URL  https://workout.wip.tokyo/
E-mail  hello@wip.tokyo
////////////////////////////////////////
EOT;
                    sendMail($from,$to,$subject,$comment);
                    header("Location:index.php");
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
<?php require('header.php'); ?>

    <main>

        <div class="form-container">
            <form class="form" method="post" action="">
                <h2 class="title">パスワード変更</h2>
                <div class="area-msg"><?php echo getErrMsg('common'); ?></div>
                <div class="form-body">
                    <label>
                    古いパスワード
                    <div class="area-msg"><?php echo getErrMsg('old_password'); ?></div>
                    <input type="password" name="old_password">
                    </label>
                    <label>
                    新しいパスワード
                    <div class="area-msg"><?php echo getErrMsg('new_password'); ?></div>
                    <input type="password" name="new_password">
                    </label>
                    <label>
                    新しいパスワード再入力
                    <div class="area-msg"><?php echo getErrMsg('new_password_retype'); ?></div>
                    <input type="password" name="new_password_retype">
                    </label>
                    <input type="submit" name="submit" value="変更する">
                </div>
            </form>
        </div>



    </main>
    <?php require('footer.php'); ?>