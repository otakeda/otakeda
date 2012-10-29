<?php
/** 
* カテゴリ２階層目を取得する
*/
ob_start();
require_once("cate_para.php");
require_once("cate_sub.php");
?>
<?php

	// 直接呼び出した場合のサンプル表示用
	$cate_url="http://rssfeed.deecorp.jp/dem/category/001.rss";
	if ($url=="") $url=$cate_url;
	if (is_null($url)) $url=$cate_url;

	// 案件一覧部分のHTML取得
	get2ndCategory($url);

	
$content=ob_get_contents();
ob_end_clean();
echo $content;

?>
 
