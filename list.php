<?php
if(!$_GET['clubid']){
    header("Location:./");
}
require_once '../../include/dsn.php';
$mdb2=MDB2::factory($dsn);
if(PEAR::isError($mdb2)){
    die($mdb2->getMessage());
}
$clubid=addslashes($_GET['clubid']);
$sql="SELECT * FROM admins WHERE clubid=?";
$str=$mdb2->prepare($sql,array('text'),MDB2_PREPARE_RESULT);
$data=array($clubid);
$res=$str->execute($data);
if(PEAR::isError($res)){
    die($res->getMessage());
}
if($res->numRows()!==1){
    die("そのidは存在しません。");
}
$row=$res->fetchRow(MDB2_FETCHMODE_OBJECT);
$title=  htmlspecialchars($row->title);
$detail=  htmlspecialchars($row->detail);
$deadline=substr($row->deadline,0,10);
$id=$row->id;
$estatus="";
$link="#form_page";
if($row->status==1){
     $estatus="<span style='color:red;'>（締切り済み）</span>";
     $link="#";
}
$sql="SELECT * FROM members WHERE adminid=?";
$str=$mdb2->prepare($sql,array('integer'),MDB2_PREPARE_RESULT);
$data=array($id);
$res=$str->execute($data);
$h_clubid=  htmlspecialchars($clubid);?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
        <meta name="robots"　content="noindex,nofollow">
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
        <title>サークル出欠簿</title>
    </head>
<?
$html=<<<EOM
    <body>
    <div data-role="page" id="main_page">
    <div data-role="header"><h1>出欠簿</h1></div>
    <div data-role="content" data-theme="a">
    <h2>{$title}{$estatus}</h2>
    <p>{$detail}</p>
    <p>回答期限：{$deadline}</p>
    <p align="right"><a href="#qa" data-transition="slide" data-direction=“reverse”>>>>よくある質問</a></p>
    <div class="ui-grid-a" style="border-bottom:1px solid #fff;">
        <div class="ui-block-a" align="center">名前</div>
        <div class="ui-block-b" align="center">出欠</div>
    </div>
EOM;
$rownum=1;
while($row=$res->fetchRow(MDB2_FETCHMODE_OBJECT)){
    //$name=array($row->name);
    $name=  htmlspecialchars($row->name);
    $comment=  htmlspecialchars($row->comment);
    $status=$row->status;
    if($status==0){
        $status_tag="<a href='{$link}' data-transition=\"slide\" data-direction=\“reverse\” data-role='button' id='row$rownum' onClick='member_id($rownum);member_name(\"$name\");'>回答</a><span class='memid' style='display:none;'>{$row->id}</span>";
        $comment="ここにコメントが出るよ。シカト？(´・ω・｀)";
    }elseif($status==1){
        $status_tag="<a href='{$link}' data-transition=\"slide\" data-direction=\“reverse\” data-role='button' id='row$rownum' onClick='member_id($rownum);select_option(0);member_name(\"$name\");' data-theme='b'>出席</a><span class='memid' style='display:none;'>{$row->id}</span>";
    }else{
        $status_tag="<a href='{$link}' data-transition=\"slide\" data-direction=\“reverse\” data-role='button' id='row$rownum' onClick='member_id($rownum);select_option(1);member_name(\"$name\");' data-theme='e'>欠席</a><span class='memid' style='display:none;'>{$row->id}</span>";
    }
    
    $html.=<<<EOM
    <div class="ui-grid-a">
        <div class="ui-block-a"><h3>$name({$row->sp}SP)</h3></div>
        <div class="ui-block-b">$status_tag</div>
    </div>
    <p style="font-size:9pt;border-bottom:1px dashed #fff; padding:0 0 20px 0;margin:0;">$comment</p>
EOM;
    $rownum++;
}
$html.=<<<EOM
</div>
</div>
<div data-role="page" id="form_page" data-theme='a'>
    <div data-role="header"data-position="inline">
        <a data-transition="slide" data-direction="reverse" href="#main_page" data-icon="back">戻る</a>
        <h1>出欠簿</h1>
    </div>
    <div id='comment'>
    <h3></h3>
        <form action="change.php" method="POST" data-ajax="false">
            <textarea name="comment" type="text" placeholder='何かコメントあれば'></textarea>
            <input name="memberid" type="hidden">
            <input name="clubid" type="hidden" value="$clubid">
            <select name="status" id="status">
                <option value="1">出席</option>
                <option value="2">欠席</option>
            </select>
            <input type="submit"value="回答">
        </form>
    </div>
</div>
<div data-role="page" id="qa" data-theme='a'>
    <div data-role="header"data-position="inline">
        <a data-transition="slide" data-direction="reverse" href="#main_page" data-icon="back">戻る</a>
        <h1>よくある質問</h1>
    </div>
<div data-role="content">

<h3>Q.1　このサイトは何？</h3>
<p>このサイトはサークルの出欠を取るためのサイトです。ボタン押すぐらいならできるよね？ねぇ？</p>
<h3>Q.2　SPって何？</h3>
<p>出欠確認を無視すると貯まる悲しいポイントです。3pt貯まると暗黒の儀式が行われます。</p>
<h3>Q.3　なぜ背景が黒いの？</h3>
<p>制作者が暗黒面に落ちてしまったからです。</p>
</div>
</div>
</body>
</html>
EOM;
$script=<<<EOM
<script>
    function member_id(row_number){
    $("input[name='memberid']").val("");
    var selector="#row"+row_number;
    var num=$('.memid',$(selector).parent()).html();
    $("input[name='memberid']").val(num);
    }
    function select_option(num){
        $('#status option').eq(num).attr({selected:'selected'});
    }
    function member_name(name){
        $('#comment h3').empty();
        $('#comment h3').append(name+"の出欠");
    }
    $(".close").click(function(){
        $("input[name='memberid']").val("");
    });
</script>
EOM;
$mdb2->disconnect();
echo$html.$script;
include_once("analyticstracking.php");
?>