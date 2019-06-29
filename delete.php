<?php
require('function.php');
debug('================================');
debug('削除ページ');
debug('================================');
debugLogStart();

require('auth.php');

if(!empty($_GET['id'])){
    $post_id = sanitize($_GET['id']);
debug($post_id);

    try{
        $dbh = dbConnect();
        $sql = 'UPDATE post SET delete_flg=1 WHERE id=:id';
        $data = array(':id'=>$post_id);
        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
            debug('index.phpへ遷移');
            header("Location:index.php");
        }
    }catch(Exception $e){
        error_log('エラー発生'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}else{
    debug('サニタイズエラー。index.phpへ遷移します');
    header("Location:index.php");
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>