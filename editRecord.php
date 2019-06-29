<?php
require('function.php');
debug('================================');
debug('ログ編集ページ');
debug('================================');
debugLogStart();

require('auth.php');

$dbFormData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($dbFormData,true));

if(!empty($_GET['id'])){
    $post_id = sanitize($_GET['id']);

    if(!empty($_POST)){
        debug('POST送信あり');
        debug('POST情報：'.print_r($_POST,true));

        $item_name = $_POST['item_name'];
        $weight = $_POST['weight'];
        $total_rep = $_POST['total_rep'];

        try{
            $dbh = dbConnect();
            $sql = 'UPDATE post SET item_name=:item_name, weight=:weight, total_rep=:total_rep WHERE id=:id';
            $data = array(':item_name'=>$item_name,':weight'=>$weight,':total_rep'=>$total_rep,':id'=>$post_id);
            $stmt = queryPost($dbh,$sql,$data);

            if($stmt){
                $_SESSION['msg_success'] = SUC03;
                debug('投稿ページへ遷移');
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
<section class="overflow">
<?php require('header.php'); ?>
    <main>
        <div class="form-container">
            <form class="form" method="post" action="">
                <h2 class="title">ワークアウトログ編集</h2>
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
                    <input type="submit" name="submit" value="記録する">
                    </div>
            </form>
        </div>
    </main>
    <?php require('footer.php'); ?>