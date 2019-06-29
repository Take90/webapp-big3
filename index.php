<?php
require('function.php');
debug('================================');
debug('投稿ページ');
debug('================================');
debugLogStart();
require('auth.php');

if(!empty($_POST)){
    debug('post送信あり');

    //変数に入力情報を代入
    $item_name = $_POST['item_name'];
    $weight = $_POST['weight'];
    $total_rep = $_POST['total_rep'];
    //DB登録用
    $total_weight = $weight*$total_rep;

    //未入力チェック
    validRequired($item_name,'item_name');
    validRequired($weight,'weight');
    validRequired($total_rep,'total_rep');

    if(empty($err_msg)){
        debug('バリデーションOK');
        try{
            debug('ワークアウトログをDBへ記録します');
            $dbh = dbConnect();
            $sql = 'INSERT INTO post (item_name, weight, total_rep, total_weight, user_id, create_at) VALUES (:item_name,:weight,:total_rep,:total_weight,:user_id,:create_at)';
            $data = array(
                ':item_name'=>$item_name,
                ':weight'=>$weight,
                ':total_rep'=>$total_rep,
                ':total_weight'=>$total_weight,
                ':user_id'=>$_SESSION['user_id'],
                ':create_at'=>date('Y-m-d H:i:s')
            );
            $stmt = queryPost($dbh,$sql,$data);
            if($stmt){
                debug('セッション変数の中身：'.print_r($_SESSION,true));
                header("Location:index.php");
            }
        }catch(Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
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
                <h2 class="title">ワークアウトログ</h2>
                <div class="form-body">
                    <label class="post-select">
                    <i class="fas fa-dumbbell"></i> 種目名
                    <select name="item_name" id="item_name"  class="select">
                        <option value="ベンチプレス">ベンチプレス</option>
                        <option value="スクワット">スクワット</option>
                        <option value="デッドリフト">デッドリフト</option>
                    </select>
                    </label>
                    <label class="post-select">
                    <i class="fas fa-weight-hanging"></i> 重量(kg)
                    <select name="weight" id="weight" class="select">
                    <?php
                    $count = 20;
                    while($count<101){
                        echo "<option value=\"".$count."\">".$count."</option>";
                        $count+=5;
                    }
                    ?>
                    </select>
                    </label>
                    <label class="post-select">
                    <i class="fas fa-calculator"></i> 合計回数
                    <select name="total_rep" id="total_rep"  class="select">
                    <?php
                    $count = 1;
                    while($count<51){
                        echo "<option value=\"".$count."\">".$count."</option>";
                        $count+=1;
                    }
                    ?>
                    </select>
                    </label>
                    <input type="submit" value="記録する">
                    </div>
            </form>
        </div>
        <div class="record">
            <?php require('record.php'); ?>
        </div>
    </main>
    <?php require('footer.php'); ?>