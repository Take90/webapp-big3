<?php
require('function.php');
debug('================================');
debug('最大挙上重量ページ');
debug('================================');
debugLogStart();

//ログイン認証
require('auth.php');

//DB用変数
$user_id = $_SESSION['user_id'];

//DB接続開始
try{
    $dbh = dbConnect();
    $sql = 'SELECT item_name,MAX(weight) FROM post WHERE user_id=:user_id AND delete_flg=0 GROUP BY item_name';
    $data = array(':user_id'=>$user_id);
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetchAll();
    debug('クエリ結果の中身：'.print_r($result,true));
}catch(Exception $e){
    error_log('エラー発生'.$e->getMessage());
    $err_msg['common'] = MSG07;
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php require('head.php'); ?>
<body>
<?php require('header.php'); ?>

    <main>
        <h2 class="title">最大挙上重量</h2>
        <?php foreach($result as $onerm): ?>
        <article class="total-weight">
            <div class="post-container">
                <h4><?php echo $onerm['item_name']; ?></h4>
                <p><i class="fas fa-medal"></i> <?php echo $onerm['MAX(weight)']."kg"; ?></p>
            </div>
        </article>
<?php endforeach; ?>
    </main>
    <?php require('footer.php'); ?>