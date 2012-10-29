<?php
ob_start();

require_once("lib/YahooOAuth.inc");
require_once("cate_header.php");
require_once("cate_html.php");
require_once("cate_para.php");
require_once("cate_sub.php");
require_once("cate_auth.php");
/**
 *   案件一覧画面
 */ 

	$is_login=false;


	$text=null;
	$status_id=null;
	$to=null;
	$user_id=null;
	$screen_name=null;
	$userimg=null;
	$q_status_id=null;

//	$is_login=false;
//	if ($is_login) $textdisable=""; else $textdisable="disabled";

//  Yahoo認証部分
//  callbackurlとしてこのアプリのURLを
	$scriptname="/yahoo/catelist.php";
//	$callback = sprintf('http://%s%s',$_SERVER['SERVER_NAME'],$scriptname);  
//	$callback = sprintf('http://%s%s',$_SERVER['SERVER_NAME'],$_SERVER['SCRIPT_NAME']);  
//var_dump($_SERVER['SCRIPT_NAME']);
try{
	if (!preg_match('/index/',$_SERVER['SCRIPT_NAME']))  //indexとついていたら認証やらない
	{
	$session = YahooSession::requireSession($consumer_key, $consumer_secret, $callback);
//var_dump($session);
	if (!is_null($session))
	{
		$api =new cateListApi($session);
		$parameters =array(
		'page' => 1);
		$response = $api->getResponse($parameters); 
//	print "<h1>response</h1><p>".$response."</p>";
		$is_login=true;
	}
	else
	{
		print "<h2>案件情報をご覧になるには、こちらからログインしてください</h2>\n";
		print "<a href=".$scriptname."><img src=\"http://i.yimg.jp/images/login/btn/btnLYid.gif\" width=\"366\" height=\"38\"alt=\"Yahoo! JAPAN IDでログイン\" border=\"0\"></a>\n";
		print "<br /><br />\n";
	}
	}
	else
	{
		print "<h2>案件情報をご覧になるには、こちらからログインしてください</h2>\n";
		print "<a href=".$scriptname."><img src=\"http://i.yimg.jp/images/login/btn/btnLYid.gif\" width=\"366\" height=\"38\"alt=\"Yahoo! JAPAN IDでログイン\" border=\"0\"></a>\n";
		print "<br /><br />\n";
	}

}
catch(Catchable $e)
{
	print "ERROR:(".$e->getMessage().")\n";
}
// カテゴリ一覧取得
	if ($is_login)
//	if ((SERVENV=="hon")||($is_login))
	{
	print "<div id=\"category_list_on\">\n";
	getTopCategory();
	print "</div> <!-- category_list_on -->\n";
//	print "<a href=\"?\">ログアウト</a>\n";
	}

require_once("cate_footer.php");
$content=ob_get_contents();
ob_end_clean();
echo $content;

?>
