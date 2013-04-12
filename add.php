<?php
require_once '../../include/dsn.php';
$mdb2=MDB2::factory($dsn);
if(PEAR::isError($mdb2)){
    die($mdb2->getMessage());
}
$name=  addslashes($_POST['name']);
$adminid=  addslashes($_POST['adminid']);
$sql="INSERT INTO members (adminid,name)VALUES(?,?)";
$str=$mdb2->prepare($sql,array('integer','text'),MDB2_PREPARE_MANIP);
$data=array($adminid,$name);
$res=$str->execute($data);
if(PEAR::isError($res)){
    die($res->getMessage());
}
$mdb2->disconnect();
header("Location:admin.php");
?>