<?php
ob_start();

require_once("corpheader.php");
require_once("corphtml.php");
require_once("corppara.php");
require_once("corpfnc.php");
require_once("corpauth.php");

	$is_login=false;

	$pretext = PRETEXT;
	$presearch =PRESEARCH;
	$precorp=PRECORP;
	$preurl=PREURL;
	$pretag=PRETAG;
	$preaddress=PREADDRESS;

	$text=null;
	$status_id=null;
	$to=null;
	$user_id=null;
	$screen_name=null;
	$userimg=null;


	print "  <!-- #columnR -->\n";
	print "  <div id=\"columnR\">\n" ;

// 投稿するときは$toつくっておく
        if ((!is_null($twitext))&&($twitext!="")) $create_to=true; else $create_to=false;
//RTのときも
	if ($sid > 0) $create_to=true;

	print "    <!-- #twitterLogin -->\n";
	print "    <div id=\"twitterLogin\">\n" ;

	$member_id=0;
	try{
		$member_id= twiAuth($db, $to, $user_id,$screen_name,$consumer_key,$consumer_secret,$create_to,$userimg,$agree_flag);
	}catch(Exception $e) {  
		print "<p>TwitterAPIアクセスエラー: <a href=\"?logoff=clear\">もう一度</a><br />一部ブラウザでログイン後の動作が不安定な場合があります。繰り返しエラーが出る場合、別のブラウザを使ってみてください</p>\n";
	}
	if ($member_id > 0) $is_login=true;
//var_dump($corpid);

	print "      <!-- 角丸下部 -->\n";
	print "      <div id=\"twitterLoginBottom\"> </div><!-- /角丸下部 -->\n";
	print "    </div>\n" ;
	print "    <!-- /#twitterLogin -->\n";

//corpidパラメータがあるかどうか
	if ($corpid > 0) 
	{
		if (!getCorpInfo($db,$corpid, $corp_name, $address, $url,$corp_tag,$searchtext)) $corpid=0;
	}

// Post & db access

	if ($is_login) $textdisable=""; else $textdisable="disabled";

/*
	print "    <div class=\"detail\">\n";
	print "      <p style=\"padding-top:12px;font-size:12px;\">\n";
//	print "      <p>\n";
	print "      検索結果の一番下から新規企業登録ができます。<span style=\"color:#777777;\" >"
	."本サイトの情報は皆さんの会社の評判やクチコミについての投稿と、各社URLから自動取得したデータを"
	."元にしています。</span></p>\n";
	print "    </div>\n";
 
	listMembers($db);
 
	if (!$is_login)
	print "    <p style=\"margin-top:8px;\">※企業のクチコミや企業情報を登録するには、ページ上部よりTwitter IDでログインしてください。</p>\n";
*/
        if ((!is_null($twitext))&&($twitext!="")&&($is_login)&&(!is_null($corp_name))) 
	{ 
// 投稿があった場合
		if (mb_strpos($twitext,$pretext,0,'UTF-8'))
	print "<br />(".mb_strpos($twitext,$pretext,0,'UTF-8').") ERROR <br />".$pretext."の部分を消して書き換えてください\n";
		else	
		{
			if (!($corpid>0)) $corpid = newCorpid($db);
			$fixed=" http://s.deecorp.jp/?c=".$corpid." ";
			
			if ($corpid>0) 
			{
try{
				$status_id=postToTwitter($twitext.$fixed, $to,null);
}catch(Exception $e)
{  
	print "<p>TwitterAPIアクセスエラー</p>\n";
	$status_id=0;
}
				if ($status_id>0) //Twitterへの投稿がうまくいったときだけInsert
				{
					insertCorps($db,$status_id, $twitext,$user_id,$user_type_id,$corp_name,
						$corpid,$url,$corp_tag,$address,$user_star,$searchtext);
				}
			}
			else print "     <br />企業ID取得エラー\n";
		}
		if ($corpid>0)
		print "    <br /><a href=\"?c=".$corpid."\">元のページにもどる</a>\n";
		else
		print "    <br /><a href=\"?\">元のページにもどる</a>\n";
	}
	else {
//投稿がなかった場合
		if (!$corpid >0) {
// トップページで検索してるとき
			$corpid="";
			$corp_name=$precorp;
			$url=$preurl;
			$corp_tag=$pretag;
			$address=$preaddress;
		}
		else {
// corp_idあり

try{
			if ($sid>0) $status_id=reTwitter($to,$sid,$twitext);
// sidあり＝retweet
}catch(Exception $e) {  
			print "<p>TwitterAPIアクセスエラー</p>\n";
			$status_id=0;
}
			$twitext=trim(preg_replace("/https?:\/\/s.deecorp.jp\/[a-zA-Z0-9\.\/\?=:%,!#~*@&_\-]* *$/iu","",$twitext));
//var_dump($twitext);
			if ($status_id>0) insertCorps($db,$status_id, $twitext,$user_id,'0',$corp_name,
						$corpid,$url,$corp_tag,$address,0,$searchtext);
			print "    <br /><a href=\"?c=".$corpid."\">元のページにもどる</a>\n";
		}

	}
	print "  <div id=\"snslink\">\n";
	print "  <table frame=void>\n";
//google Plus one
        print "    <tr><td>\n";
/*
        print "<script type=\"text/javascript\" src=\"https://apis.google.com/js/plusone.js\">\n";
        print "   {lang: 'ja'}\n";
        print "</script>\n";
        print "<g:plusone></g:plusone>\n";
*/
	print "<div class=\"g-plusone\" data-annotation=\"inline\" data-width=\"200\" data-href=\"http://s.deecorp.jp/\"></div>\n";

	print "<script type=\"text/javascript\">\n";
	print "  window.___gcfg = {lang: 'ja'};\n";

	print "  (function() {\n";
	print "    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;\n";
	print "    po.src = 'https://apis.google.com/js/plusone.js';\n";
	print "    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);\n";
	print "  })();\n";
	print "</script>\n";
	print "    </td></tr>\n";

//Twitter
	print "    <tr><td>\n";
	print "<a href=\"http://twitter.com/share\" class=\"twitter-share-button\" data-url=\"http://s.deecorp.jp/\" data-count=\"horizontal\" data-via=\"q2dee\" data-lang=\"ja\">Tweet</a><script type=\"text/javascript\" src=\"http://platform.twitter.com/widgets.js\"></script>\n";
	print "    </td></tr>\n";
//facebook
	print "    <tr><td>\n";
	print "<iframe src=\"http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fs.deecorp.jp%2F&amp;send=false&amp;layout=button_count&amp;width=200&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=35\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:200px; height:35px;\" allowTransparency=\"true\"></iframe>\n";
	print "    </td></tr>\n";
/*
//mixi
	print "    <tr><td>\n";
	print "<iframe scrolling=\"no\" frameborder=\"0\" allowTransparency=\"true\" style=\"overflow:hidden; border:0; width:140px; height:20px\" src=\"http://plugins.mixi.jp/favorite.pl?href=http%3A%2F%2Fs.deecorp.jp%2F&service_key=918cdd7f75fc326bb4bd9fb3432f20659da9eae3&show_faces=false&width=140\"></iframe>\n";
	print "    </td></tr>\n";
*/
        print "    <tr><td>最新情報・問い合わせはこちらのTwitterアカウントへ<br />\n";
        print "<a href=\"http://twitter.com/q2dee\" class=\"twitter-follow-button\" data-show-count=\"false\" data-lang=\"ja\">Follow @q2dee</a>\n";
	print "<script src=\"http://platform.twitter.com/widgets.js\" type=\"text/javascript\"></script>\n";
        print "    </td></tr>\n";

	print "  </table>\n";
	print "  </div>\n";
	print "  <!-- /#snslink -->\n";
	print "  </div>\n";
	print "  <!-- /#columnR -->\n";


//画面の左側
//var_dump($searchword);
	if ($searchword==$presearch) $searchword="";
	if ($searchword=="") $searchtext=$presearch;
	else $searchtext=$searchword;

	print "  <!-- #columnL -->\n";
	print "  <div id=\"column1\">\n" ;

//	print "    <div id=\"tumblelog\" class=\"clearfix\">\n";

	print "      <!-- /#tabArea -->\n";
	print "      <div class=\"tabbody\">\n";
// 最新のクチコミリスト
			$rec_count=0;
//			$rec_count=searchVideo($db,$searchword,null,null,$nowpage1,$userimg,$member_id);
			$rec_count=searchEntries($db,$searchword,null,null,$nowpage1,$userimg,$member_id);


	print "      </div>\n";  //tabbody
	print "      <!-- /#tabbody -->\n";
// タブ構成おわり
	print "  </div>\n";
	print "  <!-- /#columnL  -->\n";

//左おわり
//最後にデフォルトのタブと文字数カウント
	print"<script type=\"text/javascript\">\n";
	print"        try{\n";
	print"      seltab('box','head',2,".$defbox.");\n";
//        if (!((!is_null($twitext))&&($twitext!="")))   // 投稿がないときだけ
//	print"        wc(document.frmtwi.twitext.value); \n";
	if ($user_id > 0) print"        setTwitterID('".$user_id."'); \n";
	print"        }catch (e){}\n";
	print"</script>\n";


//	require_once("corpfooter.php");
$content=ob_get_contents();
ob_end_clean();
echo $content;

?>
