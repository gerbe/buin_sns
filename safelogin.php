<?php
require_once'../../include/dsn.php';
$mdb2=MDB2::factory($dsn);
if(PEAR::isError($mdb2)){
    die($mdb2->getMessage());
}
session_start();
if($_SESSION['sid']){
    $sql="SELECT * FROM admins WHERE sid=?";
    $str=$mdb2->prepare($sql,array('text'),MDB2_PREPARE_RESULT);
    $data=array($_SESSION['sid']);
    $res=$str->execute($data);
    if(PEAR::isError($res)){
        die($res->getMessage());
    }
    if($res->numRows()!==1){//パターン1：sidが正しいものでないからセッション破壊
        $_SESSION = array();
        if (isset($_COOKIE["PHPSESSID"])) {
            setcookie("PHPSESSID", '', time() - 1800, '/');
        }
        session_destroy();
        header("Location:admin.php");
    }else{//パターン2：sidが正しい、新しいsidを発行してdbもアプデする
        $row=$res->fetchRow(MDB2_FETCHMODE_OBJECT);
        $id=$row->id;
        session_regenerate_id();
        //print_r($row);
        $_SESSION['sid']=addslashes(md5(time().session_id()));
        $sql="UPDATE admins SET sid=? WHERE id=?";
        $str=$mdb2->prepare($sql,array('text','integer'),MDB2_PREPARE_MANIP);
        $data=array($_SESSION['sid'],$id);
        $res=$str->execute($data);
        if(PEAR::isError($res)){
            die($res->getMessage());
        }
        $pattern=1;
    }
}else{//パターン3:sidが存在しない
    $pattern=2;
}