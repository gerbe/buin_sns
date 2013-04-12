<?php
require_once '../../include/dsn.php';
$mdb2=MDB2::factory($dsn);
if(PEAR::isError($mdb2)){
    die($mdb2->getMessage());
}
$title=  addslashes($_POST['title']);
$detail=  addslashes($_POST['detail']);
$id=  addslashes($_POST['id']);
$deadline=  addslashes($_POST['deadline']." 00:00:00");
/*echo$title;
echo$detail;
echo$id;
echo$deadline;*/
$sql="UPDATE admins SET title=?,detail=?,deadline=?,status=0 WHERE id=?";
$str=$mdb2->prepare($sql,array('text','text','text','integer'),MDB2_PREPARE_MANIP);
$data=array($title,$detail,$deadline,$id);
$res=$str->execute($data);
if(PEAR::isError($res)){
    die($res->getMessage());
}
$sql="UPDATE members SET status=0,comment='',restime='0000-00-00 00:00:00' WHERE adminid=?";
$str=$mdb2->prepare($sql,array('integer'),MDB2_PREPARE_MANIP);
$data=array($id);
$res=$str->execute($data);
if(PEAR::isError($res)){
    die($res->getMessage());
}
$mdb2->disconnect();
header("Location:admin.php");
?>
