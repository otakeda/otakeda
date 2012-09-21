<?php
ob_start();

require_once("corpheader.php");
require_once("qahtml.php");
require_once("corppara.php");
require_once("qafnc.php");
require_once("corpauth.php");

	$is_login=false;

	$pretext = PRETEXT;
	$presearch =PRESEARCH;
	$pretitle=PRETITLE;
	$preurl=PREURL;
	$precategory=PRECATEGORY;

	$text=null;
	$status_id=null;
	$to=null;
	$user_id=null;
	$screen_name=null;
	$userimg=null;
	$q_status_id=null;


	print "  <!-- #columnR -->\n";
	print "  <div id=\"columnR\">\n" ;

// 投稿するときは$toつくっておく
        if ((!is_null($twitext))&&($twitext!="")) $create_to=true; else $create_to=false;

	print "    <!-- #twitterLogin -->\n";
	print "    <div id=\"twitterLogin\">\n" ;

	$member_id=0;
	try{
	$member_id= twiAuth($db, $to, $user_id,$screen_name,$consumer_key,$consumer_secret,$create_to,$userimg,$agree_flag);
	}catch(Exception $e)
	{  
		print "<p>TwitterAPIアクセスエラー: <a href=\"?logoff=clear\">もう一度</a><br />一部ブラウザでログイン後の動作が不安定な場合があります。繰り返しエラーが出る場合、別のブラウザを使ってみてください</p>\n";
	}
	if ($member_id > 0) $is_login=true;
//var_dump($qid);

	print "      <!-- 角丸下部 -->\n";
	print "      <div id=\"twitterLoginBottom\"> </div><!-- /角丸下部 -->\n";
	print "    </div>\n" ;
	print "    <!-- /#twitterLogin -->\n";

//qidパラメータがあるかどうか
	if ($qid > 0) getQInfo($db, $qid, $q_title, $category, $url,$q_status_id);

// Post & db access

	if ($is_login) $textdisable=""; else $textdisable="disabled";


	print "    <div class=\"detail\">\n";
	print "      <p style=\"padding-top:12px;font-size:12px;\">\n";
	print "       1. 左から参加したいグループで<font color=red>「参加希望」</font><br />";
	print "       2. <font color=red>７名</font>集まったら、こちらから非公開グループ(*1)の参加をご案内<br />";
	print "       3. グループ内でお互いに<font color=red>情報交換</font>しながら製品検証(*2)";
	print "    </div>\n";
	print "      ＊1 非公開グループでの情報交換はこちらから配布するIDを利用していただきます。\n";
	print " (参加前に再度参加意思確認します。ドタキャン可）<br />";
	print "      ＊2 通常1ヶ月程度で終了します。結果の一部は本サイト上で公開します <br /></p>\n";
	print "<br />\n";
 
//	listMembers($db);
 
/*
	if (!$is_login)
	print "       本サイトは会員登録はありません。ＴｗｉｔｔｅｒのIDで参加できます。右上からログインしてください";
//	print "    <p style=\"margin-top:8px;\">ページ上部よりTwitter IDでログインしてください。</p>\n";
*/

	$cateid=null;
//	showCategory($db,$cateid);

        if ((!is_null($twitext))&&($twitext!="")&&($is_login)&&(!is_null($q_title))) 
	{ 
//var_dump($q_title);
// 投稿があった場合
		if (mb_strpos($twitext,$pretext,0,'UTF-8'))
	print "<br />(".mb_strpos($twitext,$pretext,0,'UTF-8').") ERROR <br />".$pretext."の部分を消して書き換えてください\n";
		else	
		{
//			if (!($qid>0)) $qid = newQid($db);
			if ($qid>0) $fixed=" http://myfan.jp/?q=".$qid;
			else $fixed=" http://myfan.jp/";
			
try{

			$status_id=postToTwitter($twitext.$fixed, $to,null);
			if ($status_id>0)
			{
				if ($qid>0) 
				{
					insertAnswer($db,$member_id,$status_id, $twitext ,$qid);
				}
				else
				{
					insertQuestion($db,$member_id,$status_id, $twitext,$q_title,$url,$category);
				}
			}
			else throw new Exception("Twitter投稿エラー");
}catch(Exception $e)
{
		print "ERROR:".$e->getMessage();
}
		}
		if ($qid>0)
		print "    <br /><a href=\"?c=".$qid."\">元のページにもどる</a>\n";
		else
		print "    <br /><a href=\"?\">元のページにもどる</a>\n";
	}
	else
	{
//投稿がなかった場合
		if (!$qid >0)
		{
		$qid="";
		$q_title=$pretitle;
		$url=$preurl;
		$category=$precategory;
//		$address=$preaddress;
		}
	}
	print "  <table >\n";
//Twitter
	print "    <tr><td>\n";
	print "<a href=\"http://twitter.com/share\" class=\"twitter-share-button\" data-url=\"http://s.deecorp.jp/\" data-count=\"horizontal\" data-via=\"q2dee\" data-lang=\"ja\">Tweet</a><script type=\"text/javascript\" src=\"http://platform.twitter.com/widgets.js\"></script>\n";
	print "    </td></tr>\n";
//facebook
	print "    <tr><td>\n";
	print "<iframe src=\"http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fs.deecorp.jp%2F&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=35\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:450px; height:35px;\" allowTransparency=\"true\"></iframe>\n";
	print "    </td></tr>\n";
	print "  </table>\n";
	print "  </div>\n";
	print "  <!-- /#columnR -->\n";
// twitter form end

//画面の左側
//var_dump($searchword);
	if ($searchword==$presearch) $searchword="";
	if ($searchword=="") $searchtext=$presearch;
	else $searchtext=$searchword;

	$ranks = array();
	print "  <!-- #columnL -->\n";
	print "  <div id=\"columnL\">\n" ;

//	$ranks=showRankname($db,$rank);  //ランキングカテゴリの表示
//var_dump($ranks);

/*
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
//	print "      <input type=\"hidden\" name=\"nowpage1\" value=\"".$nowpage1."\" />\n";
	print "      </div>\n";
	print "      <!-- /検索 -->\n";
	print "    </form>";   // formsearch
*/

	print "    <!-- #tweetArea -->\n";
	print "    <div id=\"tweetArea\">\n";
	print "      <!-- #tabArea -->\n";
	print "      <div class=\"tabbody\">\n";
	print "        <form method=\"post\" name=\"frmtwi\" action=\"?\">\n";
//	print "        <div id=\"box1\" name=\"selbox\">\n";
		if ($defbox==1)
		{
		$rec_count=0;
			if ($qid>0) 
			$rec_count=searchEntries($db,null,null,$qid,1,$userimg,$member_id,$ranks,$rank);
			else
			$rec_count=searchEntries($db,$searchword,null,null,$nowpage1,$userimg,$member_id,$ranks,$rank);

		}
//	print "        </div>\n";


//ログインしているときだけ自分のを表示
/*
//var_dump($is_login);
		if (($is_login)&&($defbox==2))
		{
	print "        <div id=\"box2\" name=\"selbox\">";
			if ($qid>0) 
				$rec_count=searchEntries($db,null,null,$qid,1,$userimg,$member_id);
			else
				$rec_count=searchEntries($db,null,$user_id,null,$nowpage2,$userimg,$member_id);
			print "        </div>\n";
		}
*/

	print "<input type=\"hidden\" name=\"nowpage1\" value=\"".$nowpage1."\" />"; // all
	print "<input type=\"hidden\" name=\"nowpage2\" value=\"".$nowpage2."\" />"; // my
	print "<input type=\"hidden\" name=\"asin\" value=\"0\" />";
	print "<input type=\"hidden\" name=\"defbox\" value=\"".$defbox."\" />";
	print "        </form>\n";
	print "      </div>\n";  //tabbody
	print "      <!-- /#tabbody -->\n";
// タブ構成おわり

	print "    </div>\n";
	print "    <!-- /#tweetArea -->\n";

	print "    <div id=\"columnLbottom\"></div>\n";  //角丸下部

	print "    </form>\n";
	print "  </div>\n";
	print "  <!-- /#columnL  -->\n";

//左おわり
//最後にデフォルトのタブと文字数カウント
/*
	print"<script type=\"text/javascript\">\n";
	print"        try{\n";
	print"      seltab('box','head',2,".$defbox.");\n";
//        if (!((!is_null($twitext))&&($twitext!="")))   // 投稿がないときだけ
//	print"        wc(document.frmtwi.twitext.value); \n";
	print"        }catch (e){}\n";
	print"</script>\n";
*/


	require_once("qafooter.php");
$content=ob_get_contents();
ob_end_clean();
echo $content;

?>

