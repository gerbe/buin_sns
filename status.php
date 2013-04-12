<?php
//メンバーのステータス変更用
require_once '../../include/dsn.php';
$mdb2=MDB2::factory($dsn,$option);
if(PEAR::isError($mdb2)){
    die($mdb2->getMessage());
}
echo"接続に成功しました";
$sql="INSERT INTO  (id)VALUES(1)";
$res=$mdb2->query($sql);
if(PEAR::isError($res)){
    die($res->getMessage());
}
$mdb2->disconnect();
?>