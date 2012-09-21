<?php
ob_start();

require_once("corpheader.php");
require_once("corppara.php");
require_once("corpfnc.php");

	$is_login=false;

//  corpjson.php  corpscript.jsから。検索時の自動サジェスト機能のため
	$text=null;
	$status_id=null;
	$to=null;
	$user_id=null;
	$screen_name=null;


	$nowpage2=1;
// Post & db access

	$rec_count=0;
	$rec_count=searchEntriesJson($db,$searchword);

//	var_dump( $searchword);
// タブ構成おわり

        pg_close($db);
$content=ob_get_contents();
ob_end_clean();
echo $content;

?>
