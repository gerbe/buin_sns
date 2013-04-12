<?php
require_once'safelogin.php';
if($pattern===1){
        $title=  htmlspecialchars($row->title);
        $detail=  htmlspecialchars($row->detail);
        $deadline= substr($row->deadline,0,10);
        $estatus="";
        $disabled="";
        $clubid=$row->clubid;
        if($row->status==1){
            $estatus="<span style='color:red;'>（締切り済み）</span>";
            $disabled="disabled";
        }elseif($row->status==2){
            $disabled="disabled";
            $title="点呼は下のボタンから";
            $title="未設定";
            $deadline="未設定";
            $detail="未設定";
        }
        $html=<<<EOM
        <!DOCTYPE html>
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta name="description" content="サークルの出欠が管理できる出欠簿です。シカトするとポイントが貯まる悲しい仕様になっています。スマホ最適です。">
                <meta name="keywords" content="サークル,出欠,ツール,シカト">
                <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
                <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css">
                <link rel="stylesheet" type="text/css" href="http://dev.jtsage.com/cdn/datebox/latest/jqm-datebox.min.css" />
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
                <script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
                <script type="text/javascript" src="http://dev.jtsage.com/cdn/datebox/latest/jqm-datebox.core.min.js"></script>
                <script type="text/javascript" src="http://dev.jtsage.com/cdn/datebox/latest/jqm-datebox.mode.calbox.min.js"></script>
                <script type="text/javascript" src="http://dev.jtsage.com/cdn/datebox/i18n/jquery.mobile.datebox.i18n.ja.utf8.js"></script>
                <title>管理画面<<出欠簿</title>
            </head>
            <body>
            <div data-role="page" id="admin" data-url="admin.php">
            <div data-role="header"data-position="inline">
                <a rel="external" href="list.php?clubid={$clubid}">出欠簿</a>
                <h1>管理画面</h1>
                <a rel="external" href="logout.php" class="ui-btn-right">ログアウト</a>
            </div>
            <div data-role="content">
                <h3>現在の点呼状況</h3>
                <p>題名：{$title}{$estatus}</p>
                <p>詳細：{$detail}</p>
                <p>〆切：{$deadline}</p>
                <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <a href="#form_admin" data-role="button">点呼開始</a>
                    </div>
                    <div class="ui-block-b">
                        <a href="sime.php" onClick="return confirm('本当に出欠を締め切りますか？');" style="text-decoration:none;" rel="external"><button {$disabled}>締め切り</button></a>
                    </div>
                </div>
                <div data-role="collapsible">
                    <h3>メンバーの追加</h3>
                    <p>
                        <form action="add.php" method="POST">
                            <input name="name" type="text" placeholder="メンバー名">
                            <input name="adminid" type="hidden" value="{$id}">
                            <input type="submit" value="追加">
                        </form>
                    </p>
                </div>
EOM;
        $html.="<table><thead><tr><th></th><th></th><th></th></tr></thead><tbody>";
        $sql="SELECT * FROM members WHERE adminid=?";
        $str=$mdb2->prepare($sql,array('integer'),MDB2_PREPARE_RESULT);
        $data=array($row->id);
        $id=$row->id;
        $res=$str->execute($data);
        if(PEAR::isError($res)){
            die($res->getMessage());
        }
        while($row=$res->fetchRow(MDB2_FETCHMODE_OBJECT)){
            if($row->status==0){
                $status="未";
                $restime="";
            }elseif($row->status==1){
                $status="出";
                $restime=  htmlspecialchars($row->restime,ENT_QUOTES);
            }else{
                $status="欠";
                $restime=  htmlspecialchars($row->restime,ENT_QUOTES);
            }
            $name=  htmlspecialchars($row->name,ENT_QUOTES);
            //$status=  htmlspecialchars($row->status,ENT_QUOTES);
            $sp=  htmlspecialchars($row->sp,ENT_QUOTES);
            $comment=  htmlspecialchars($row->comment,ENT_QUOTES);
            
            $html.="<tr><td>{$name}({$sp})</td><td>$status</td><td>$restime</td></tr>";
        }
        $html.=<<<EOM
            </tbody></table>
            <p align="right"><a href="list.php?clubid={$clubid}">出欠簿</a>（メンバー用URL）</p>
            </div>
           </div>
                            
            <div data-role="page" id="form_admin">
                <div data-role="header"data-position="inline">
                    <a href="#admin" data-icon="back">戻る</a>
                    <h1>出欠簿</h1>
                </div>
                <div data-role="content">
                    <form action="restart.php" method="POST" data-ajax="false">
                        <input name="id" type="hidden" value="{$id}">
                        <label for="title">タイトル</label>
                        <input name="title" type="text" id="title" placeholder="ex.)定例ミーティング">
                        <label for="detail">詳細</label>
                        <textarea name="detail" id="detail" placeholder="ex.)引継ぎを行います。"></textarea>
                        <label for="cal">締め切り</label>
                        <input name="deadline" id="cal" type="text" data-role="datebox" data-options='{"mode":"calbox", "overrideDateFormat":"%Y-%m-%d","useModal":"true","minDays":"0"}' value="" />
                        <input type="submit" value="点呼開始">
                    </form>
                </div>
            </div>
EOM;
}elseif($pattern===2){
    if($_GET['message']=="fail"){
        $message="ログインに失敗しました";
    }
    $html=<<<EOM
    <!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <meta name="description" content="サークルの出欠が管理できる出欠簿です。シカトするとポイントが貯まる悲しい仕様になっています。スマホ最適です。">
            <meta name="keywords" content="サークル,出欠,ツール,シカト">
            <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
            <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
            <script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
        <title>出欠りすと管理画面</title>
        </head>
        <body>
            <div data-role="page" id="login_page">
                <div data-role="header">
                    <h3>出欠簿</h3>
                </div>
                <div class="alert_area" style="margin:0; padding:0;">
                    <p style="color:red;margin:0;paddin:0;"align="center">$message</p>
                </div>
                <h3>管理者ログイン</h3>
                <div data-role="content">
                    <form action="login.php" method="POST" data-ajax="false">
                        <input name="clubid" type="text" placeholder="サークルID"><br>
                        <input name="password" type="password" placeholder="パスワード"><br>
                        <input type="submit" value="ログイン">
                    </form>
                </div>
            </div>
EOM;
}
$html.="</body></html>";
$mdb2->disconnect();
print($html);
include_once("analyticstracking.php");
?>