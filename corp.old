<?php
ob_start();

require_once("corphtml.php");
require_once("corppara.php");
require_once("corpheader.php");
require_once("corpfnc.php");
require_once("qaauth.php");

	$is_login=false;

	if (isset($_POST['defbox'])) $defbox = $_POST['defbox']; 
	else 
	{
	if (isset($_GET['defbox'])) $defbox = $_GET['defbox']; else $defbox=1;
	}

//	$pretext = "(会社のクチコミ情報)";
	$pretext = PRETEXT;
	$presearch =PRESEARCH;

//	$precorp="(例:ディーコープ株式会社)"; 
	$precorp=PRECORP;
//	$preurl="(例: http://deecorp.jp/)"; 
	$preurl=PREURL;
//	$pretag="(例:DeeCorp,見積,経営支援,リバースオークション)";  
	$pretag=PRETAG;
//	$preaddress="(例: 東京都港区)";
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

	print "    <!-- #twitterLogin -->\n";
	print "    <div id=\"twitterLogin\">\n" ;

	$member_id= twiAuth($db, $to, $user_id,$screen_name,$consumer_key,$consumer_secret,$create_to,$admin_user_id,$userimg);
	if ($member_id > 0) $is_login=true;
	print "      <!-- 角丸下部 -->\n";
	print "      <div id=\"twitterLoginBottom\"> </div><!-- /角丸下部 -->\n";
	print "    </div>\n" ;
	print "    <!-- /#twitterLogin -->\n";

//corpidパラメータがあるかどうか
	if ($corpid > 0) getCorpInfo($db,$corpid, $corp_name, $address, $url,$corp_tag);
/*
	else 
	{
	}
*/

// Post & db access

	if ($is_login) $textdisable=""; else $textdisable="disabled";


	print "    <div class=\"detail\">\n";
	print "      <p style=\"padding-top:18px;\">\n";
	print "      企業名をクリックするとその企業情報のクチコミ情報の参照・書き込みができます。検索結果の一番下から新規企業登録もできます</p>\n";
	print "    </div>\n";
	print "    <p class=\"center\" style=\"margin-bottom:6px;\"><img src=\"images/img_next.gif\" width=\"80\" height=\"51\" alt=\"投稿画面\" /></p>\n";
 
	print "    <h4 title=\"ユーザリスト\">0企業のクチコミを投稿</h4>\n";
	listMembers($db);
 
	if (!$is_login)
	print "    <p style=\"margin-top:8px;\">※企業のクチコミや企業情報を登録するには、ページ上部よりTwitter IDでログインしてください。</p>\n";

        if ((!is_null($twitext))&&($twitext!="")&&($is_login)&&(!is_null($corp_name))) 
	{ 
// 投稿があった場合
		if (mb_strpos($twitext,$pretext,0,'UTF-8'))
	print "<br />(".mb_strpos($twitext,$pretext,0,'UTF-8').") ERROR <br />".$pretext."の部分を消して書き換えてください\n";
		else	
		{
			$status_id=postToTwitter($twitext, $to,null);
			if ($status_id>0)
			{
/*
				if ($url == $preurl) $url="";
				if ($address == $preaddress) $address="";
				if ($corp_tag == $pretag) $corp_tag="";
*/
				insertCorps($db,$status_id, $twitext,$user_id,$user_type_id,$corp_name,
					$corpid,$url,$corp_tag,$address,$user_star);
			}
		}
		print "    <br /><a href=\"?c=".$corpid."\">元のページにもどる</a>\n";
	}
	else
	{
//投稿がなかった場合
		if (!$corpid >0)
		{
		$corpid="";
		$corp_name=$precorp;
		$url=$preurl;
		$corp_tag=$pretag;
		$address=$preaddress;
		}
	}
	print "  </div>\n";
	print "  <!-- /#columnR -->\n";
// twitter form end

//画面の左側
//var_dump($searchword);
	if ($searchword==$presearch) $searchword="";
	if ($searchword=="") $searchtext=$presearch;
	else $searchtext=$searchword;

	print "  <!-- #columnL -->\n";
	print "  <div id=\"columnL\">\n" ;
	print "    <form method=\"post\" name=\"frmsearch\" action=\"corp.php\">\n";
	print "      <!-- 検索 -->\n";
	print "      <div id=\"search\" title=\"企業名やキーワードを検索\">\n" ;
	print "        <h2>企業名やキーワードを検索</h2>\n" ;
	print "        <p><input id=\"text\" type=\"text\" name=\"searchword\" size=\"30\" maxlength=\"40\" value=\""
		.h($searchtext)."\"  autocomplete=\"off\" "
		."\" onFocus=\"clearText(this); \" onBlur=\"setEx(this,'".$presearch."');\" style=\"color:#555\"/>\n";
	print "        <input type=\"submit\" name=\"searchpost\" value=\"検索\" class=\"cursorPointer\" />\n";
	print "        </p>";
	print "        <div id=\"suggest\" onclick=\"document.frmsearch.submit();\" onselect=\"document.frmsearch.submit();\"></div>\n";
	print "        <p>";
	print "        <span style=\"text-align:center\">↑会社名(ひらがな),URL(一部)で候補がでてきます</span>";
	print "      </p>\n";
	print "      <input type=\"hidden\" name=\"nowpage2\" value=\"".$nowpage2."\" />\n";
	print "      <input type=\"hidden\" name=\"nowpage3\" value=\"".$nowpage3."\" />\n";
	print "      </div>\n";
	print "      <!-- /検索 -->\n";
	print "    </form>";   // formsearch

	print "    <!-- #tweetArea -->\n";
	print "    <div id=\"tweetArea\">\n";
	print "      <!-- #tabArea -->\n";
	print "      <div id=\"tabArea\">\n";
	print "        <ul>\n";
	print "        <li id=\"head1\" name=\"selhead\" title=\"新着\">\n";
	if ($defbox==1)
//	print "          <span>新着のクチコミ</span></li>\n";
//	print "          <a href=\"javascript:seltab('box', 'head', 2, 1)\"><span>新着のクチコミ</span></a></li>\n";
	print "          <span>新着のクチコミ</span></li>\n";
		else
//	print "          <a href=\"javascript:seltab('box', 'head', 2, 1)\"><span>新着のクチコミ</span></a></li>\n";
	print "          <a href=\"?defbox=1\"><span>新着のクチコミ</span></a></li>\n";

	if ($is_login)
	{
	print "          <li id=\"head2\" name=\"selhead\" title=\"あなたの\">\n";
		if ($defbox==2)
//	print "          <span>あなたのクチコミ</span></li>\n";
//	print "          <a href=\"javascript:seltab('box', 'head', 2, 2)\"><span>あなたの投稿</span></a></li>\n";
	print "          <span>あなたの投稿</span></li>\n";
		else
//	print "          <a href=\"javascript:seltab('box', 'head', 2, 2)\"><span>あなたのクチコミ</span></a></li>\n";
	print "          <a href=\"?defbox=2\"><span>あなたの投稿</span></a></li>\n";
	}

	print "        </ul>\n";
	print "      </div>\n";
	print "      <!-- /#tabArea -->\n";


	print "      <div class=\"tabbody\">\n";
	print "        <form method=\"post\" name=\"frmtwi\" action=\"corp.php\">\n";
	print "        <div id=\"box1\" name=\"selbox\">\n";
	if ($defbox==1)
	{
// うまく動かないのではずす
//	print "        <span id=\"sresult\">\n";
//	print "        </span>\n";
	$rec_count=0;
	if ($corpid>0) 
	$rec_count=searchEntries($db,null,null,$corpid,$nowpage2,$userimg);
	else
	$rec_count=searchEntries($db,$searchword,null,null,$nowpage2,$userimg);

//	print "<p>(keyword=[".$searchword."] ".$rec_count."件)</p>\n";
	}
	print "        </div>\n";


//ログインしているときだけ自分のを表示
//var_dump($is_login);
	if (($is_login)&&($defbox==2))
	{
	print "        <div id=\"box2\" name=\"selbox\">";
		if ($corpid>0) 
		$rec_count=searchEntries($db,null,null,$corpid,$nowpage3,$userimg);
		else
		$rec_count=searchEntries($db,null,$user_id,null,$nowpage3,$userimg);
//	print "        <p>(".$rec_count."件)</p>\n";
	print "        </div>\n";
	}

	print "<input type=\"hidden\" name=\"nowpage2\" value=\"".$nowpage2."\" />"; // all
	print "<input type=\"hidden\" name=\"nowpage3\" value=\"".$nowpage3."\" />"; // my
	print "<input type=\"hidden\" name=\"asin\" value=\"0\" />";
	print "<input type=\"hidden\" name=\"defbox\" value=\"".$defbox."\" />";
//	var_dump( $ama_xml);
	print "        </form>\n";
	print "      </div>\n";  //tabbody
	print "      <!-- /#tabbody -->\n";
// タブ構成おわり

	print "    </div>\n";
	print "    <!-- /#tweetArea -->\n";

	print "    <div id=\"columnLbottom\"></div>\n";  //角丸下部

//	print "    <span id=\"sresult\"> list </span>";
	print "    </form>\n";
	print "  </div>\n";
	print "  <!-- /#clumnL  -->\n";

//左おわり、右はじまり
//最後にデフォルトのタブと文字数カウント
	print"<script type=\"text/javascript\">\n";
	print"      seltab('box','head',2,".$defbox.");\n";
        if (!((!is_null($twitext))&&($twitext!="")))   // 投稿がないときだけ
	print"        wc(document.frmtwi.twitext.value); \n";
	print"</script>\n";

//	print "<br /><a href=\"corp.php\">top page</a><br />";

	require_once("corpfooter.php");
$content=ob_get_contents();
ob_end_clean();
echo $content;

?>
