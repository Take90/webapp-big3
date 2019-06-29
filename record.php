<?php
//DB用変数
$user_id = $_SESSION['user_id'];

//DB接続開始
try{
    $dbh = dbConnect();
    $sql = 'SELECT id,create_at,item_name,weight,total_rep,total_weight FROM post WHERE user_id=:user_id AND delete_flg=0 ORDER BY create_at DESC';
    $data = array(':user_id'=>$user_id);
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetchAll();
    debug('クエリ結果の中身：'.print_r($result,true));
}catch(Exception $e){
    error_log('エラー発生:'.$e->getMessage());
    $err_msg['common'] = MSG07;
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
        <?php
        foreach($result as $post):
        $post_id = $post['id'];
        $create_at = $post['create_at'];
        $item_name = $post['item_name'];
        $weight = $post['weight'];
        $total_rep = $post['total_rep'];
        ?>
        <article class="post">
            <div class="post-container">
                <time><?php echo $create_at; ?></time>
                <h4><?php echo $item_name; ?></h4>
                <p><?php echo $weight; ?>kg×<?php echo $total_rep; ?>回=<?php echo ($weight*$total_rep); ?>kg</p>
                <button type="button" class="edit_btn" onclick="location.href='editRecord.php?id=<?php echo $post_id; ?>'">編集</button>
                <button type="button" class="delete_btn" onclick="location.href='delete.php?id=<?php echo $post_id; ?>'">削除</button>
            </div>
        </article>
        <?php endforeach; ?>