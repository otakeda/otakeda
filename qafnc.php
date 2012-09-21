<?php

//require_once "HTTP/Request.php";
function h($str)
{
	$str=htmlspecialchars($str,ENT_QUOTES);
	$str = str_replace("\r\n","<br />",$str);
	$str = str_replace("\n","<br />",$str);
	return ($str);
}
function pgs($str)
{
	return pg_escape_string($str);
}

function hsc($str) {
    return htmlspecialchars($str, ENT_QUOTES);
}
function urlencode_rfc3986_0($str)
{

    return str_replace('%7E', '~', rawurlencode($str));
}
function errmsg($msg)
{
        print "\n<br><font color=blue>ERROR: ".h($msg)."</font><br>";
}

//  カテゴリをリンク付きで全部表示
function showCategory($db,$cateid=null)
{
	$r=array('aa');
	$nid=null;
	$addwhere="";
//	if (ADMIN_MEMBER_ID!=$member_id) $addwhere = " and m.member_id= ".pgs($member_id);
//var_dump($addwhere);
	$select1 = "select category_id,category_name from qa_category q"
		." where delete_flg!='1' "
		." order by category_id";
        $rows = pg_query($db, $select1);
        while($row = pg_fetch_assoc($rows))
	{
		$rid=$row['category_id'];
		$r[$rid]  = $row['category_name'];
		if ($cateid==$row['category_id']) 
		print "<font size=+3>".$row['category_name']."</font>";
		else
		print "<a href=\"?cateid=".$row['category_id']."\" >"
		.$row['category_name']."</a>\n";
	}
	return $r;
}
//  DeleteFlgをセット
function deleteEntry($db,$repid,$delete_flg)
{
	$nid=0;
	$select1 = "update qa_a set delete_flg ='".pgs($delete_flg)."' where rep_id = ".pgs($repid);  
        $rows = pg_query($db, $select1);
	if (pg_affected_rows($rows)==1)
	{
		print "[\n";
		print "  {\n";
		print "  repid:".$repid."\n";
		print "  ,\n";
		print "  delete_flg:".$delete_flg."\n";
		print "  }\n";
		print "]\n";
	}
	else errmsg("delete error repid:".$repid);
	return $nid;
}
//  現在のDeleteFlgの値を確認  MEMBERIDも使って、更新していいデータかを確認
function selectDel($db,$repid,$member_id)
{
	$nid=null;
	$addwhere="";
	if (ADMIN_MEMBER_ID!=$member_id) $addwhere = " and m.member_id= ".pgs($member_id);
//var_dump($addwhere);
	$select1 = "select r.delete_flg from qa_a r"
		." join members m on r.user_id=m.user_id"
		.$addwhere
		." where r.rep_id = ".pgs($repid);
        $rows = pg_query($db, $select1);
        while($row = pg_fetch_assoc($rows))
	{
		$nid = $row['delete_flg'];
	}
	if (is_null($nid)) errmsg("no repid:".$repid);
//var_dump($nid);
	return $nid;
}
//  新規登録時のみ企業IDを生成	
function newQid($db)
{
	$nid=0;
	$select1 = "select nextval('q_id_seq') as nextcid";  
        $rows = pg_query($db, $select1);
        while($row = pg_fetch_assoc($rows))
	{
		$nid = $row['nextcid'];
	}
	return $nid;
}
//  Aの保存
function insertAnswer($db, $member_id,$status_id, $a_text, $q_id)
{
	$select1 = "insert into qa_a(status_id,member_id,q_id,a_text,"
		."update_date) values('"
	.pgs($status_id)."',".pgs($member_id).",".pgs($q_id).",'".pgs($a_text)."',now())";
//var_dump($select1);
	$rows = pg_query($db,$select1);
//var_dump($rows);
	if (pg_affected_rows($rows)==1)
	{
		printf("投稿されました");
	}
	else  errmsg("DB Access ERROR");
}
//  Qの保存
function insertQuestion($db, $member_id,$status_id, $q_text, $q_title, $url,$category)
{
	$select1 = "insert into qa_q(status_id,member_id,q_title,q_text,url,"
		."update_date,category) values('"
	.pgs($status_id)."',".pgs($member_id).",'".$q_title."','".pgs($q_text)."','"
	.pgs($url)."',now(),'".pgs($category)."' )";
//var_dump($select1);
	$rows = pg_query($db,$select1);
//var_dump($rows);
	if (pg_affected_rows($rows)==1)
	{
		printf("投稿されました");
	}
	else  errmsg("DB Access ERROR");
}
// q_idをキーに、企業情報もってくる
function getQInfo($db,$qid, &$q_title, &$category, &$url,&$q_status_id)
{
	
        $select1 = "select q.q_id,q.q_title, q.status_id ,q.update_date "
	.",q.category,q.url from qa_q q"
	." where q_id = '".pgs($qid)."' ";
//var_dump($select1);
        $rows = pg_query($db, $select1);
        $i=0;   //会社の数

        while($row = pg_fetch_assoc($rows))
        {
		$i++;
		$q_status_id=$row['status_id'];
		$q_title=$row['q_title'];
		$category=$row['category'];
		$url=$row['url'];
	}
	if ($i > 0) return true ; else return false;
}

// 質問の登録画面表示
function inputQuestion($member_id)
{
        $pretitle=PRETITLE;
        $preurl=PREURL;
        $precategory=PRECATEGORY;

	$qid="";
	$title_name=$pretitle;
	$url=$preurl;
	$category=$precategory;

	if ($member_id>0) $disabled=""; else $disabled="disabled";
        print "    <table>\n";
        print "    <tr><th colspan=2 style=\"color:red;\">見出し *(５文字以上必須)</th></tr><tr><td colspan=2 style=\"text-align:center;\" >\n";
        print "    <input type=\"text\" name=\"q_title\" maxlength=\"100\" style=\"color:#555;width:298px;\" maxlength=\"100\" ".$disabled
                ." value=\"".$title_name."\" onFocus=\"clearText(this);\" onClick=\"checkValue();\" onKeyup=\"checkValue();\" onBlur=\"setEx(this,'".$pretitle."');\" />\n";
        print "    </td></tr>\n";

        print "    <tr><th colspan=2  style=\"color:red;\">カテゴリ *(必須)</th></tr><tr><td colspan=2 style=\"text-align:center;\" >\n";
        print "    <input type=\"text\" name=\"category\" maxlength=\"100\" ".$disabled." onBlur=\"setEx(this,'".$precategory."');\" "
                ." value=\"".$category."\" onFocus=\"clearText(this);\" onClick=\"checkValue();\" onKeyup=\"checkValue();\" style=\"color:#555;width:298px;\"/>\n";
        print "    </td></tr>\n";

        print "    <tr><th colspan=2>参考URL</th></tr><tr><td style=\"text-align:center;\" colspan=2>\n";
        print "    <input type=\"text\" name=\"url\" maxlength=\"100\" ".$disabled." onBlur=\"setURL(this,'".$preurl."');\""
                ." value=\"".$url."\" onFocus=\"clearURL(this); \"  style=\"color:#555;width:298px;\"/>\n";
        print "    </td></tr>\n";

	print "    </table>\n";
}

// 入力欄(クチコミ情報のみ)
function inputTweet($userimg)
{
	if (is_null($userimg)) $is_login=false; else $is_login=true;
	if ($is_login) $textdisable =""; else $textdisable="disabled";
	
	print "      <!-- TweetList -->\n";
	print "      <div class=\"twitterList\">\n";
	print "        <div class=\"userIcon\">\n";
	if ($is_login)
	print "          <img border=\"0\" width=\"42\" height=\"42\" src=\"".$userimg."\" alt=\"icon\" />";
	else
	print "          <img border=\"0\" width=\"42\" height=\"42\" src=\"images/mark_no.jpg\" alt=\"icon\" />";

	print "        </div>\n";
	print "        <div class=\"tweet\">\n";
	print "          <div class=\"tweetTop\">\n";	

	$pretext = "(会社のクチコミ情報)";
	print "            <table>\n";
        print "              <tr>\n";
        print "              <th colspan=2>文字数(1-110): <span id=\"wordcounter\" style=\"color:red; font-size:16pt; font-weight:bold;\">0</span></th>\n";
        print "              </tr>\n";
        print "              <tr>\n";
	if ($is_login)
	{
        print "              <td colspan=2><textarea name=\"twitext\" rows=\"5\" cols=\"30\" onkeyup=\"document.frmtwi.twipost.disabled=false; wc(this.value);\" onclick=\"wc(this.value)\" onFocus=\"clearText(this);\" style=\"color:#555;;width:280px;\" >".$pretext."</textarea></td></tr>\n";
        print "              <tr><td colspan=2><button name=\"twipost\" disabled onclick=\"this.disabled=true; valueCheckSubmit();\">";
        print "Twitterと同時投稿</button></td></tr>\n";
	print "              <tr><td><span style=\"color:#AAA;\">Tweetには自動で直リンクURLが付加されます</span></td></tr>\n";
	}
	else
	{
        print "              <td colspan=2><textarea name=\"twitext\" rows=\"5\" cols=\"30\" onkeyup=\"document.frmtwi.twipost.disabled=false; wc(this.value);\" onclick=\"wc(this.value)\" disabled onFocus=\"clearText(this);\" style=\"color:#555;;width:280px;\" >(TwitterIDでログイン後、投稿できます)</textarea></td></tr>\n";
	}	
        print "            </table>\n";
	print "            <p class=\"tweetInfo\"></p>\n";
	print "          </div>\n"; //tweetTop
	print "          <div class=\"tweetBottom\"><!-- --></div>\n";
	print "        </div>\n";  //tweet
	print "      <div class=\"clear\"><!-- --></div>\n";
	print "      </div>\n";    //tweetList
}
// 画面右の利用者リスト
function listMembers($db)
{
       	$select1 = "select member_id,user_id,screen_name,imgfile,name,pts "
		." from members m"
		." order by member_id desc";

        $rows = pg_query($db, $select1);
	$numrows=pg_num_rows($rows);
	$j=0;
//		print "           最近登録したユーザ\n";
        while($row = pg_fetch_assoc($rows))
	{
		if (($row['name']=="")||(is_null($row['name'])))
		$name=$row['screen_name'];
		else
		$name=$row['name'];

//		print "           <p><a href=\"?uid=".$row['user_id']."\">";
		print "           <p>";
		print "<img src=\"" .$row['imgfile']."\" border=0 alt=\"".$name."\" width=20 height=20 />\n";
		print "           <a href=\"http://twitter.com/".$row['screen_name']."\" target=_blank>";
		print "           ".$name ;
		print "           </a>";
		if (($row['pts']=="")||(is_null($row['pts']))) $pts=0;
		else $pts = $row['pts'];
		if ($pts > 2) print " <img src=\"images/ico_star.gif\" alt=\"".$pts."\" title=\"".$pts."\">";
		if ($pts > 5) print " <img src=\"images/ico_star.gif\" alt=\"".$pts."\" title=\"".$pts."\">";
		if ($pts > 8) print " <img src=\"images/ico_star.gif\" alt=\"".$pts."\" title=\"".$pts."\">";
		print "           </p>\n";
		
		$j++;
		if ($j > 10) break;
	}
}
// Q&Aリスト	
function searchEntries($db, $searchword = '', $user_id,$q_id,$nowpage=1,$userimg=null,$member_id=0,$ranks,$rank) 
{
	$addwhere = "";
//var_dump($q_id);

	if (is_null($searchword)) $searchword="";
	if ($q_id>0) $addwhere=" and q.q_id = '".pgs($q_id)."' ";
	else
	{
//  条件なしの検索時は最近のデータしかみせない
		if (($searchword=="")&&is_null($user_id))  $addwhere .= " and q.update_date > now() - interval'3000 days'";
	}
//var_dump($addwhere);

 	$tabcounter=0;
	$npc="1";  //all
       	$select1 = "select q.status_id as q_status_id,q.category,"
		." q_title, q.q_id, q.q_text, q.update_date ,m.member_id"
		.",m.screen_name,q.url ,m.imgfile as mimg ,q.delete_flg"
		.",q.choice1,q.choice2,q.choice3,q.choice4,q.choice5"
		.",count(distinct a1.a_id) as cnt1 "
		.",count(distinct a2.a_id) as cnt2 "
		.",count(distinct a3.a_id) as cnt3 "
		." from qa_q q"
		." left join members m on m.member_id=q.member_id"
		." left join qa_a a1  on a1.q_id=q.q_id and a1.choice=1"
		." left join qa_a a2  on a2.q_id=q.q_id and a2.choice=2"
		." left join qa_a a3  on a3.q_id=q.q_id and a3.choice=3"
		." where q.q_title like '%".pgs($searchword)."%' "
//		.$addwhere
		." group by  "
       		." q.status_id ,q.category,"
		." q_title, q.q_id, q.q_text, q.update_date ,m.member_id"
		.",m.screen_name,q.url ,m.imgfile ,q.delete_flg"
		.",q.choice1,q.choice2,q.choice3,q.choice4,q.choice5"
		." order by q.q_id";


//var_dump($select1);

        $rows = pg_query($db, $select1);
        $i=0;   //会社の数
        $j=0;   //データ行数
	$lastq=0;
	$scrtext = "";
	$numrows=pg_num_rows($rows);
	$nextflg=true;
	$morerec=false;
	$q_title=null;
        while($row = pg_fetch_assoc($rows))
        {
		$j++;
//質問の１行目かどうかを判断
		if ($lastq!=$row['q_id'])
		{
			$q_a="q";
			$q_title=$row['q_title'];
			if (!$nextflg) print "      </div> <!-- tweet2 -->\n";  //tweet2のクローズ
			$i++;
			$nextflg=true;
			$lastq=$row['q_id'];
			if ($i <= ($nowpage-1)*10) continue;  //10件までしか表示しない javascriptにも10で実装
			if ($i > 10+($nowpage-1)*10) {$i--; $morerec=true;break;}  //10件までしか表示しない javascriptにも10で実装
			$tabcounter++;
	print "      <!-- corpInfo -->\n";
	print "      <div class=\"corpInfo\">\n";
	print "      <h3><span name=\"q_title\" style=\"font-size:120%; color: black; backgroundcolor:white\">"
			. $row['q_title']. "</span>\n";
// 企業選択
	print "        <span id=q_id style=\"font:size=2; color:#CCC;\">".h($q_id)."</span>\n";
//	print "        <a href=\"?q=" .$row['q_id']."\">" ."[書く]</a>\n";
	print "        <span name=qid style=\"font:size=2; color:#EEE;\">".h($row['q_id'])."</span>\n";
	print "      </h3>\n";
	print "      <table>\n";
	print "        <tr><th>A.".$row['choice1']." ";
	print "        </th><td>\n";
			if ($member_id > 0)
	print "<a href=\"?q=".$row['q_id']."&c=1\">Yes</a>";
	print "        </td>\n";
	print "        <td>".$row['cnt1']."人\n";
	print "        </td></tr>\n";
	print "        <tr><th>B.".$row['choice2']." ";
	print "        </th><td>\n";
			if ($member_id > 0)
	print "<a href=\"?q=".$row['q_id']."&c=2\">Yes</a>";
	print "        </td>\n";
	print "        <td>".$row['cnt2']."人\n";
	print "        </td></tr>\n";
	print "        <tr><th>C.".$row['choice3']." ";
	print "        </th><td>\n";
			if ($member_id > 0)
	print "<a href=\"?q=".$row['q_id']."&c=3\">Yes</a>";
	print "        </td>\n";
	print "        <td>".$row['cnt3']."人\n";
	print "        </td></tr>\n";

// URLがあるときだけリンク表示
			if (!is_null($row['url'])&&$row['url']!="")
			{
				$showurl=preg_replace("/https?:\/\//i","",$row['url']); //http...をとる
//				$showurl=preg_replace("/\/.*$/i","",$showurl);  //    "/"以降をとる
//var_dump($showurl);
	print "        <tr><th>URL</th>";
	print "        <td>";
	print "        <a name=\"link_".$row['q_id']."\" href=\""
			.$row['url']."\" target=_blank> ".h($showurl)."</a>\n";
	print "        </td></tr>\n";

			}

	print "      </table>\n";
	print "      </div>\n";
	print "      <!-- /corpInfo -->\n";

		}
// 前の行と同じ企業の場合
		else 
		{
			$q_a="a";
			if (is_null($q_id)&&($nextflg==true))  //企業コードの指定がないときだけ
			{
	print "      <!-- TweetList -->\n";
	print "      <div class=\"twitterList\">\n";
	print "        <div class=\"userIcon\">\n";
	print "          <img border=\"0\" width=\"30\" height=\"30\" src=\"images/mark_no.jpg\" alt=\"icon\" />";
	print "        </div>\n";
	print "        <div class=\"tweet\">\n";
	print "          <div class=\"tweetTop\">\n";	
	print "            <p>回答があります。<a href=\"javascript:showTweet('tweet2nd_".$row['q_id']."');\"" .$row['q_id']."\">すべて見る</a></p>\n";
	print "          <p class=\"tweetInfo\"></p>\n";
	print "          </div>\n";
	print "          <div class=\"tweetBottom\"><!-- --></div>\n";
	print "        </div>\n";
	print "      <div class=\"clear\"><!-- --></div>\n";
	print "      </div>\n";

        print "      <div id=\"tweet2nd_".$row['q_id']."\" style=\"display:none;\">\n";
				$nextflg=false;  //ループを抜けたときにこのdivを閉じないといけないかを判断
			}
		}

	print "      <!-- TweetList -->\n";
	print "      <div class=\"twitterList\">\n";

// nowpage変数から画面表示対象の行かを判断
                if ($i > ($nowpage-1)*10) 
		{
			//$text=$row['q_text'];
			if ($q_a=="q") 
			{	
				$text=$row['q_text'];
				$qaid=$row['q_id'];
				$status_id=$row['q_status_id'];
			}
			else 
			{
				$text=$row['a_text'];
				$qaid=$row['a_id'];
				$status_id=$row['a_status_id'];
			}

			if (!is_null($text))
			{
	print "        <div class=\"userIcon\">\n";
			if (!is_null($row['screen_name'])&&($row['screen_name']!=""))
			{
	print "          <a href=\"http://twitter.com/".$row['screen_name']
			."\" target=\"_blank\">\n";
	print "          <img border=\"0\" width=\"42\" height=\"42\" src=\""
			.$row['mimg']."\" alt=\"user\" />";
	print "          </a>\n";
			}
			else
	print "          <img border=\"0\" width=\"30\" height=\"30\" src=\"images/mark_no.jpg\" alt=\"user\" />";

	print "        </div>\n";
	print "        <div class=\"tweet\">\n";
	print "          <div class=\"tweetTop\">\n";
	print "            <p>" .h($text)."\n";
	
	print "            </p>\n";

	print "          <p class=\"tweetInfo\">\n";
			if (($member_id==$row['member_id'])||(ADMIN_MEMBER_ID==$member_id))
			{
				if ($row['delete_flg']=='1') 
	print "            <a href=\"javascript:modosu_tweet(".$qaid.");\"><img id=\"del_"
				.$qaid."\" src=\"images/modosu.jpg\" border=0 width=30 height=15/></a>\n";
				else
	print "            <a href=\"javascript:delete_tweet(".$qaid.");\"><img id=\"del_"
				.$qaid."\" src=\"images/delete.jpg\" border=0  width=30 height=15/></a>\n";
// debug用
//			print "<a href=corpdel.php?r=".$qaid." target=_blank>link</a>";
			}
//			else print $user_id.":".$row['user_id'];

			if ($status_id>0)
			{
	print "            <a href=\"http://twitter.com/".$row['screen_name']."/statuses/"
			 .$status_id."\" target=\"_blank\" title=\"Link to Twitter\">\n";
	print "            <img src=\"http://twitter-badges.s3.amazonaws.com/t_mini-b.png\" alt=\"twitter\"/></a> ";
			}
	print "          ".h($row['screen_name'])
			." - ".date('m/d H:i:s',strtotime($row['update_date']))."</p>\n";
	print "          </div>\n";
	print "          <div class=\"tweetBottom\"><!-- --></div>\n";
	print "        </div>\n";
			}
	print "      <div class=\"clear\"><!-- --></div>\n";
	print "      </div>\n";

		}

        }
	print inputTweet($userimg)."\n";
	if (!$nextflg) 
	print "      </div> <!-- tweet2 -->\n";  //tweet2のクローズ

	
	return $numrows;
}


function postToTwitter($message,$to,$replyid=null){
	$len0=mb_strlen($message,'UTF-8');
/*
	$twi0 = str_replace("\r\n","-",$message);
	$len1 = mb_strlen($twi0, 'UTF-8');   //改行を１文字としたときの長さ
	if ($len1 < $len0) $rngap = $len0-$len1; else $rngap=0;
*/

        if ($len0 <1)
		{print"<font color=#FF3333>1文字以上入力してください</font>";
		print "<table border=\"1\" width=\"300\"><tr><td bgcolor=\"#CCCCCC\">".$message."</td></tr></table>\n";
		return; }
        if ($len0>140)
                {print"<font color=#FF3333>140文字以内にしてください</font>";
		print "<table border=\"1\" width=\"300\"><tr><td bgcolor=\"#cccccc\">".$message."</td></tr></table>\n";
		return;
		}

	
try{
        if (is_null($replyid))
        {
		$req = $to->OAuthRequest("https://api.twitter.com/1/statuses/update.xml",
//		$req = $to->OAuthRequest("http://api.twitter.com/1/statuses/update.xml",
		"POST",array("status"=>$message));
        }
        else
        {
        print "REPLYID".$replyid;
        $req = $to->OAuthRequest("https://api.twitter.com/1/statuses/update.xml",
                "POST",array("status"=>$message,
                "in_reply_to_status_id"=>$replyid));
        }
        $xml = simplexml_load_string($req);
//var_dump($xml);
	$created_at =$xml->created_at; // 標準時 9 hours late
        $status_id = $xml->id;  // 呟きのステータスID
        $text = $xml->text;  // 呟き
        $source = $xml->source; //tweetするアプリ
        $reply_status_id=$xml->in_reply_to_status_id; //返信元ID
        $reply_user_id=$xml->in_reply_to_user_id; //返信元USERID
        $reply_name=$xml->in_reply_to_screen_name; //返信元NAME

        if ($status_id>0) print "<table border=\"1\"><tr><td>".h($text)."</td></tr></table>\n";
        else print "<br><font color=#FF3333>投稿ERROR(同一内容の連続投稿はエラーになります)</font><br>";
	if (is_null($text)) $text="";
//	print "IN=".mb_strlen($message,'UTF-8')."/ OUT=".mb_strlen($text,'UTF-8') . "<br >\n";
} catch (Exception $e)   //Twitterへのアクセスエラー
{
	$status_id=null;
        print "<br><font color=#FF3333>Twitterアクセスエラー(もう一度やり直してみてください)</font><br>";
}

        return $status_id;
}


?>

