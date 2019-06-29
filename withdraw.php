<?php
require('function.php');
debug('================================');
debug('退会ページ');
debug('================================');
debugLogStart();

//ログイン認証
require('auth.php');

//DB用変数
$user_id = $_SESSION['user_id'];

if(!empty($_POST)){
    debug('POST送信あり');
    try{
        $dbh = dbConnect();
        $sql1 = 'UPDATE users SET delete_flg=1 WHERE id=:user_id';
        $sql2 = 'UPDATE post SET delte_flg=1 WHERE user_id=:user_id';
        $data = array(':user_id'=>$user_id);
        $stmt1 = queryPost($dbh,$sql1,$data);
        $stmt2 = queryPost($dbh,$sql2,$data);

        if($stmt1 && stmt2){
            session_destroy();
            debug('セッション変数の中身：'.print_r($_SESSION,true));
            debug('ユーザー登録ページへ遷移します');
            header("Location:signup.php");
        }else{
            debug('クエリ失敗');
            $err_msg['common'] = MSG07;
        }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
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
                <h2 class="title">退会</h2>
                <div class="form-body">
                    <input type="submit" name="submit" value="退会する">
                </div>
            </form>
        </div>



    </main>
    <?php require('footer.php'); ?>