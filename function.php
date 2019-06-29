<?php
// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');
//================================
// ログ
//================================
//ログをとる
ini_set('log_errors','off');
//ログの出力ファイル
ini_set('error_log','php.log');
//デバッグフラグ
$debug_flg = true;
function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}
//================================
// セッション準備・セッション有効期限を延ばす
//================================
//セッションファイルの置き場変更
session_save_path("/var/tmp/");
//ガベージコレクションが削除するセッションの有効期限を設定(30日以上経過しているものに対して確率100分の1で削除)
ini_set('session.gc_maxlifetim', 60*60*24*30);
//クッキー有効期限延長
ini_set('session.cookie_lifetime', 60*60*24*30);
//セッション使用
session_start();
//セッションIDを新しいものと置き換え(なりすまし対策)
session_regenerate_id();
//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart(){
    debug('>>>>>>>>>>>>>>>>>>>>>>>画面表示処理開始');
    debug('セッションID：'.session_id());
    debug('セッション変数中身：'.print_r($_SESSION,true));
    debug('現在日時タイムスタンプ： '.time());
    if(!empty($_SESSION['login_date'])&&!empty($_SESSION['login_limit'])){
        debug('ログイン期限日時タイムスタンプ：'.($_SESSION['login_date']+$_SESSION['login_limit']));
    }
}
//================================
// 定数
//================================
//エラーメッセージ
define('MSG01','入力必須です');
define('MSG02','Emailの形式で入力してください');
define('MSG03','パスワード（再入力）が合っていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上で入力してください');
define('MSG06','256文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08','こちらのEmailは既に登録されています');
define('MSG09','メールアドレスまたはパスワードが違います');
define('MSG10','古いパスワードが違います');
define('MSG11','古いパスワードと同じです');
define('MSG12','文字で入力してください');
define('MSG13','正しくありません');
define('MSG14','有効期限切れです');
define('MSG15','半角数字のみご利用いただけます');
define('SUC01','パスワードを変更しました');
define('SUC02','メールを送信しました');
define('SUC03','登録しました');
//================================
// グローバル変数
//================================
$err_msg = array();
//================================
// バリデーション関数
//================================
//未入力チェック
function validRequired($str,$key){
    if($str === ''){
        global $err_msg;
        $err_msg[$key] = MSG01;
    }
}
//メールアドレスの形式チェック
function validEmail($str,$key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
        global $err_msg;
        $err_msg[$key] = MSG02;
    }
}
//メールアドレス重複チェック
function validEmailDup($email){
    global $err_msg;
    try{
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email'=>$email);
        $stmt = queryPost($dbh,$sql,$data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty(array_shift($result))){
            $err_msg['email'] = MSG08;
        }
    }catch(Exception $e){
        error_log('エラー発生'.$e->gertMessage());
        $err_msg['common'] = MSG07;
    }
}
//同値チェック
function validMatch($str1,$str2,$key){
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}
//最小文字数チェック
function validMinLen($str,$key,$min=6){
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}
//最大文字数チェック
function validMaxLen($str,$key,$max=255){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}
//半角英数字チェック
function validHalf($str,$key){
    if(!preg_match("/^[a-zA-Z0-9]+$/",$str)){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}
//半角数字チェック
function validNumber($str,$key){
    if(!preg_match("/^[0-9]+$/",$key)){
        global $err_msg;
        $err_msg[$key] = MSG15;
    }
}
//固定長チェック
function validLength($str,$key,$len=8){
    if(mb_strlen($str) !== $len){
        global $err_msg;
        $err_msg[$key] = $len.MSG12;
    }
}
//パスワードチェック
function validPass($str,$key){
    //半角チェック
    validHalf($str,$key);
    //最大文字数チェック
    validMaxLen($str,$key);
    //最小文字数チェック
    validMinLen($str,$key);
}
//エラーメッセージの表示
function getErrMsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}
//================================
// ログイン認証
//================================
function isLogin(){
    //ログインしている場合
    if(!empty($_SESSION['login_date'])){
        debug('ログイン済みのユーザー');
        //最終ログイン日時＋有効期限＜現在日時(期限切れ)
        if(($_SESSION['login_date']+$_SESSION['login_limit']) < time()){
            debug('有効期限切れのユーザー');
            //セッション削除
            session_destroy();
            return false;
        }else{
            debug('ログイン有効期限内ユーザー');
            return true;
        }
    }else{
        debug('未ログインユーザー');
        return false;
    }
}
//================================
// データベース
//================================
//DB接続関数

function dbConnect(){
    $ini = parse_ini_file('db.ini');
    $dsn = 'mysql:dbname='.$ini['dbname'].';host='.$ini['host'].';charset=utf8';
    $user = $ini['dbusr'];
    $password = $ini['dbpass'];
    $options = array(
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_SILENT,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=>true,
    );
    //PDOオブジェクト生成
    $dbh = new PDO($dsn,$user,$password,$options);
    return $dbh;
}
//sql実行関数
function queryPost($dbh,$sql,$data){
    //クエリ作成
    $stmt = $dbh->prepare($sql);
    //プレースホルダに値をセット、sql実行
    if(!$stmt->execute($data)){
        debug('クエリ失敗');
        debug('失敗したSQL:'.print_r($stmt,true));
        $err_msg['common'] = MSG07;
        return 0;
    }
    debug('クエリ成功');
    return $stmt;
}
//ユーザー情報取得
function getUser($u_id){
    debug('ユーザー情報を取得します');
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg=0';
        $data = array(':u_id'=>$u_id);
        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}
//================================
// メール送信
//================================
function sendMail($from,$to,$subject,$comment){
    if(!empty($to) && !empty($subject) && !empty($comment)){
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");
        $result = mb_send_mail($to,$subject,$comment,"From: ".$from);
        if($result){
            debug('メール送信しました');
        }else{
            debug('エラー発生！送信失敗');
        }
    }
}
//================================
// その他
//================================
// function getSessionFlash($key){
//     if(!empty($_SESSION[$key])){
//         $data = $_SESSION[$key];
//         $_SESSION[$key] = '';
//         return $data;
//     }
// }
//サニタイズ
function sanitize($str){
    return htmlspecialchars($str,ENT_QUOTES);
}
//認証キー生成
function makeRandKey($length=8){
    static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for($i=0;$i<$length;++$i){
        $str.=$chars[mt_rand(0,61)];
    }
    return $str;
}
//フォーム入力情報保持
function getFormData($str,$flg=false){
    if($flg){
        $method = $_GET;
    }else{
        $method = $_POST;
    }
    global $dbFormData;
    //ユーザー情報あるとき
    if(!empty($dbFormData)){
        //フォームのエラーある時
        if(!empty($err_msg[$str])){
            //POSTにデータあれば
            if(isset($method[$str])){
                return sanitize($method[$str]);
            }else{
                return sanitize($dbFormData[$str]);
            }
        }else{
            //POSTに情報あるがDBの情報と異なるとき
            if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
                return sanitize($method[$str]);
            }else{
                return sanitize($dbFormData[$str]);
            }
        }
    }else{
        if(isset($method[$str])){
            return sanitize($method[$str]);
        }
    }
}
?>