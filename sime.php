<?php
require_once 'safelogin.php';
if($pattern==2){
    header("Location:admin.php");
}
$sql="UPDATE members SET sp=sp+1 WHERE adminid=? AND status=0";
$str=$mdb2->prepare($sql,array('integer'),MDB2_PREPARE_MANIP);
$data=array($id);
$res=$str->execute($data);
if(PEAR::isError($res)){
    die($res->getMessage());
}
$sql="UPDATE admins SET status=1 WHERE id=?";
$str=$mdb2->prepare($sql,array('integer'),MDB2_PREPARE_MANIP);
$data=array($id);
$res=$str->execute($data);
if(PEAR::isError($res)){
    die($res->getMessage());
}
$mdb2->disconnect();
header("Location:admin.php");
?>