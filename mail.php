<?php
$clubid="abc";
$password="abcd";
$email="v245tastes@i.softbank.jp";
mb_language("japanese");
mb_internal_encoding("UTF-8");
$to =$email;
$subject = "出席簿登録完了";
$body = "出席簿の登録が完了しました。\nクラブid:{$clubid}\nパスワード:{$password}\n出欠簿アドレス（メンバーに教えてください。）http://hsgym.m36.coreserver.jp/sp/list.php?clubid={$clubid}\n管理画面アドレスhttp://hsgym.m36.coreserver.jp/sp/admin.php";
$from = mb_encode_mimeheader(mb_convert_encoding("一橋CO-WORK","JIS","UTF-8"))."<1284coworking@gmail.com>";
mb_send_mail($to,$subject,$body,"From:".$from);
echo"成功したぽ";
?>