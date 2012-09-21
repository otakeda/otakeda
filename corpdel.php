<?php
ob_start();

//require_once("corphtml.php");
require_once("corpheader.php");
require_once("corppara.php");
require_once("corpfnc.php");
require_once("corpauth.php");

	$is_login=false;


//  Login
	$text=null;
	$status_id=null;
	$to=null;
	$user_id=null;
	$screen_name=null;

        $member_id=0;
        try{
        $member_id= twiAuthCheck($db);
//var_dump($member_id);
        }catch(Exception $e)
        {
                print "<p>TwitterAPIアクセスエラー: <a href=\"?logoff=clear\">もう一度</a><br />一部ブラウザでログイン後の動作が不安定な場合があります。繰り返しエラー
が出る場合、別のブラウザを使ってみてください</p>\n";
        }
        if ($member_id > 0) $is_login=true;

	if (($repid > 0)&&($repid <1000000000)&&($is_login))
	{	
		//  現時点のdelete_flagをとってくる
		$delete_flg=selectDel($db,$repid,$member_id);
		if (!(is_null($delete_flg))) {
			if ($delete_flg=='1') $delete_flg='0'; else $delete_flg='1';
			//  delete_flagを更新
			$rec_count=deleteEntry($db,$repid,$delete_flg);
		}
		else print "権限エラー:".$repid;
	}
	else print "認証エラー:".$repid;

//	var_dump( $searchword);
// タブ構成おわり

        pg_close($db);
$content=ob_get_contents();
ob_end_clean();
echo $content;

?>
