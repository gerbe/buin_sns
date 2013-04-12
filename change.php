<?php
//ステータスの変更
require_once '../../include/dsn.php';
$mdb2=MDB2::factory($dsn);
if(PEAR::isError($mdb2)){
    die($mdb2->getMessage());
}
$clubid=  addslashes($_POST['clubid']);
$memberid=  addslashes($_POST['memberid']);
$comment=  addslashes($_POST['comment']);
$status=  addslashes($_POST['status']);
$sql="UPDATE members SET comment=?,status=?,restime=? WHERE id=?";
$str=$mdb2->prepare($sql,array('text','integer','text','integer'));
date_default_timezone_set('Asia/Tokyo');
$data=array($comment,$status,date("Y-m-d H:i:s", time()),$memberid);
$res=$str->execute($data);
if(PEAR::isError($res)){
    die($res->getMessage());
}
$mdb2->disconnect();
header("Location:list.php?clubid=$clubid");
?>