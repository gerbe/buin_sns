<?php
require_once '../../include/dsn.php';
$clubid= addslashes($_POST['clubid']);
$password= md5(addslashes($_POST['password'])."takeuchi");
$email=  addslashes($_POST['email']);
$mdb2=MDB2::factory($dsn);
if(PEAR::isError($mdb2)){
    die($mdb2->getMessage());
}
$sql="SELECT * FROM admins WHERE clubid=?";
$str=$mdb2->prepare($sql,array('text'),MDB2_PREPARE_RESULT);
$data=array($clubid);
$res=$str->execute($data);
if(PEAR::isError($res)){
    die($res->getMessage());
}
$num=$res->numRows();
if($num>0){
    die("idがかぶっています。やり直して下さい。");
}
$sql="INSERT INTO admins (clubid,password,email) VALUES (?,?,?)";
$str=$mdb2->prepare($sql,array('text','text','text'),MDB2_PREPARE_MANIP);
$data=array($clubid,$password,$email);
$res=$str->execute($data);
if(PEAR::isError($res)){
    die($res->getMessage());
}
session_start();
$_SESSION['sid']=addslashes(md5(time().session_id()));
$sql="UPDATE admins SET sid=? WHERE clubid=?";
$str=$mdb2->prepare($sql,array('text','text'),MDB2_PREPARE_MANIP);
$data=array($_SESSION['sid'],$clubid);
$res=$str->execute($data);
if(PEAR::isError($res)){
    die($res->getMessage());
}
$mdb2->disconnect();;
mb_language("japanese");
mb_internal_encoding("UTF-8");
$to =$email;
$subject = "出席簿登録完了";
$body = "出席簿の登録が完了しました。\nクラブid:{$clubid}\nパスワード:{$_POST['password']}\n管理画面（リストの作成を行います）\nhttp://hsgym.m36.coreserver.jp/sp/admin.php\n出欠簿（メンバー用）\nhttp://hsgym.m36.coreserver.jp/sp/list.php?clubid={$clubid}";
$from = mb_encode_mimeheader(mb_convert_encoding("一橋CO-WORK","JIS","UTF-8"))."<1284coworking@gmail.com>";
mb_send_mail($to,$subject,$body,"From:".$from);
header("Location:admin.php");
?>