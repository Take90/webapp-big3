<?php
//================================
// ログイン認証・自動ログアウト
//================================
if(!empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです');

    //最終ログイン日時+有効期限<現在日時だった場合
    if(($_SESSION['login_date']+$_SESSION['login_limit']) < time()){
        debug('ログイン有効期限オーバー');
        //セッション削除(ログアウト)
        session_destroy();
        header("Location:login.php");
    }else{
        debug('ログイン有効期限内');
        //最終ログイン日時を現在日時に更新
        $_SESSION['login_date'] = time();
        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            debug('マイページへ遷移します');
            header("Location:mypage.php");
        }
    }
}else{
    debug('未ログインユーザーです');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        debug('ログインページへ遷移させます');
        header("Location:login.php");
    }
}

?>