<?php
//管理者のログイン
require_once '../../include/dsn.php';
$clubid=  addslashes($_POST['clubid']);
$password= md5(addslashes($_POST['password'])."takeuchi");
$mdb2=MDB2::factory($dsn);
if(PEAR::isError($mdb2)){
    die($mdb2->getMessage());
}
$sql="SELECT * FROM admins WHERE clubid=? and password=?";
$str=$mdb2->prepare($sql,array('text','text'),MDB2_PREPARE_RESULT);
$data=array($clubid,$password);
$res=$str->execute($data);
if(PEAR::isError($res)){
    die($res->getMessage());
}
if($res->numRows()===1){
    $row=$res->fetchRow(MDB2_FETCHMODE_OBJECT);
    $adminid=$row->id;
    session_start();
    session_regenerate_id();
    $_SESSION['sid']=addslashes(md5(time().session_id()));
    $sql="UPDATE admins SET sid=? WHERE id=?";
    $str=$mdb2->prepare($sql,array('text','integer'),MDB2_PREPARE_MANIP);
    $data=array($_SESSION['sid'],$adminid);
    $res=$str->execute($data);
    if(PEAR::isError($res)){
    die($res->getMessage());
    }
    $sql="SELECT * FROM members WHERE adminid=?";
    $str=$mdb2->prepare($sql,array('integer'),MDB2_PREPARE_RESULT);
    $data=array($adminid);
    $res=$str->execute($data);
    if(PEAR::isError($res)){
    die($res->getMessage());
    }
}else{
    $message="?message=fail";
}
$mdb2->disconnect();
header("Location:admin.php".$message);
?>