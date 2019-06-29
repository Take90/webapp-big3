<?php
require('function.php');
debug('================================');
debug('マイページ');
debug('================================');
debugLogStart();

//ログイン認証
require('auth.php');

debug('ログアウトします');
//セッション削除(ログアウト)
session_destroy();
debug('ログインページへ遷移');
header("Location:login.php");
?>