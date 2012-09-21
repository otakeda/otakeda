<?php

function n($str){
	if (is_null($str)) return true;
       	if ($str=="") return true;
       	return false;
}

function atRemove($text){
	return preg_replace("/@[a-zA-Z0-9\.\/\?=:%,!#~*@&_\-]+ /" , "" , $text);
}
function urlRemove($text){
	return preg_replace("/(https?:\/\/[a-zA-Z0-9\.\/\?=:%,!#~*@&_\-]+)/" , "" , $text);
}
function url2Link($text){
	return preg_replace("/(https?:\/\/[a-zA-Z0-9\.\/\?=:%,!#~*@&_\-]+)/" , "<a href=\"\\1\" target=\"_blank\">\\1</a>" , $text);
}
function h($str) //　改行をBRに変換
{
	$str=htmlspecialchars($str,ENT_QUOTES);
	$str = str_replace("\r\n","<br />",$str);
	$str = str_replace("\n","<br />",$str);
	return ($str);
}
function pgs($str) {
	return pg_escape_string($str);
}

function hsc($str) { return htmlspecialchars($str, ENT_QUOTES); } //シングルクォート、だぶるクォートともに変換
function hsc_decode($str) { return htmlspecialchars_decode($str, ENT_QUOTES); }

function urlencode_rfc3986_0($str) {
    return str_replace('%7E', '~', rawurlencode($str));
}
function errmsg($msg) {
        print "\n<br><font color=blue>ERROR: ".h($msg)."</font><br>";
}
// XML形式にしない  corpgraphでつかってる
function httpRequest0($request)
{
	$responsexml =null;
	if (ENV=="hon")
	{
	        $xml_string=file_get_contents($request, false);
	}
	else  //proxy
	{
		$proxy_opts = array(
		'http' => array(
		'proxy' => 'tcp://svsns20:3128',
		),
		);
		$proxy_context=stream_context_create($proxy_opts);
		$xml_string=file_get_contents($request, false,$proxy_context);
	}
	return $xml_string;
}
// XML形式で返す
function httpRequest($request)
{
/**   use PEAR lib
        $req =& new HTTP_Request("http://api.twitter.com/1/users/show.xml?screen_name=". $screen_name);
        $req->setMethod(HTTP_REQUEST_METHOD_GET);
        if (!PEAR::isError($req->sendRequest())) {
        $res = $req->getResponseBody();
        } else { $res = ""; }
        $xml=simplexml_from_string($res);
*/

	$responsexml =null;
	if (ENV=="hon")
	{
	        $xml_string=file_get_contents($request, false);
       		$responsexml=simplexml_load_string($xml_string);
	}
	else  //proxy
	{
		$proxy_opts = array(
		'http' => array(
		'proxy' => 'tcp://svsns20:3128',
		),
		);
		$proxy_context=stream_context_create($proxy_opts);
		$xml_string=file_get_contents($request, false,$proxy_context);
		$responsexml=simplexml_load_string($xml_string);
	}
	return $responsexml;
}


//  DeleteFlgをセット by corpdel.php
function deleteEntry($db,$repid,$delete_flg)
{
	$nid=0;
	$select1 = "update corprep set delete_flg ='".pgs($delete_flg)."' where rep_id = ".pgs($repid);  
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
//  現在のDeleteFlgの値を確認  MEMBERIDも使って、更新していいデータかを確認   by corpdel.php
function selectDel($db,$repid,$member_id)
{
	$nid=null;
	$addwhere="";
	if (ADMIN_MEMBER_ID!=$member_id) $addwhere = " and m.member_id= ".pgs($member_id);
//var_dump($addwhere);
	$select1 = "select r.delete_flg from corprep r"
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
function newCorpid($db)
{
	$nid=0;
	$select1 = "select nextval('corp_id_seq') as nextcid";  
        $rows = pg_query($db, $select1);
        while($row = pg_fetch_assoc($rows))
	{
		$nid = $row['nextcid'];
	}
	return $nid;
}
//  DBの保存
function insertCorps($db, $status_id, $rep_text, $user_id,$user_type_id,
		$corp_name, $corp_id, $url,$corp_tag,$address,$star,$searchtext) {

	$searchtext=getHostFromUrl($url)." ".$corp_name." ".kataToHira($corp_name);

	global $videotag;
	$select1 = "insert into corprep(status_id,corp_name,corp_id,rep_text,url,"
		."update_date,corp_tag,user_type_id,address,user_id,star,searchtext, videotag) values('"
	.pgs($status_id)."','".pgs($corp_name)."',".$corp_id.",'".hsc($rep_text)."','"
	.pgs($url)."',now() ,'".pgs($corp_tag)."','".pgs($user_type_id)."','".pgs($address)."',".pgs($user_id).",".pgs($star).",'".pgs($searchtext)."', '".hsc($videotag)."') ";
//var_dump($select1);
	$rows = pg_query($db,$select1);
	if (pg_affected_rows($rows)==1)
	{
		printf("投稿されました");
	}
	else  errmsg("DB Access ERROR");
}
// URLからhost名だけ抜き出し
function getHostFromUrl($url){
	$url=preg_replace("/https?:\/\//i","",$url); //http...をとる
	$url=preg_replace("/\/.*$/i","",$url);  //    "/"以降をとる
	return $url;
}

// 企業名をひらがな変換
function kataToHira($corp_name){
	$corp_name=trim_corp($corp_name);  //株式会社を抜き
	$hira = mb_convert_kana($corp_name,c,"UTF-8"); //全角カタカナ => 全角ひらがな
	return $hira;
}
// 企業名から"株式会社"をぬく
function trim_corp($corp_name)
{
	$corp_name=preg_replace("/[「・」／（）]/u","",$corp_name);
	$corp_name=preg_replace("/(株式会社|有限会社|合同会社|合資会社|合名会社)/","",$corp_name);
	$corp_name=trim($corp_name);
	return $corp_name;
}
// corp_idをキーに、企業情報もってくる
function getCorpInfo($db,$corpid, &$corpname, &$address, &$url,&$corptag,&$searchtext)
{
	global $videotag;	
        $select1 = "select b.corp_id,b.corp_name, b.corp_tag ,b.update_date "
	.",b.address,b.url ,b.searchtext,b.videotag from corprep b"
	." left join (select corp_id,max(update_date) as udate from corprep "
	."   where corp_id ='".pgs($corpid)."' group by corp_id ) bo "
	." on bo.corp_id=b.corp_id and bo.udate=b.update_date"
	." where b.corp_id = '".pgs($corpid)."' ";
//var_dump($select1);
        $rows = pg_query($db, $select1);
        $i=0;   //会社の数

        while($row = pg_fetch_assoc($rows))
        {
		$i++;
		$corpname=$row['corp_name'];
		$corptag=$row['corp_tag'];
		$address=$row['address'];
		$url=$row['url'];
		$searchtext=$row['searchtext'];
//		$videotag=$row['videotag'];
	}
	if ($i > 0) return true ; else return false;
}

// corpjsonから呼び出し 検索窓での自動補完
function searchEntriesJson($db, $searchword ) 
{
	if (is_null($searchword)) $searchword="";

        $tabcounter=0;
	$npc="1";  //all
        $select1 = "select corp_name, count(*) as cnt ,max(update_date) as updatedate "
		." from  corprep "
		."  where (searchtext like '% ".$searchword."%' or searchtext like '%.".$searchword."%' "
		."  or searchtext like '".$searchword."%') "
		." group by corp_name order by cnt desc,updatedate desc";
//var_dump($select1);

        $rows = pg_query($db, $select1);
        $j=0;   //データ行数
	$lastcorp=0;
	$scrtext = "";

//	print "{ \n";
	print "  [ \n";
        while($row = pg_fetch_assoc($rows))
        {
		if ($j>0) print ",\n";
		$j++;
		print "\"".$row['corp_name']."\"\n";
//		print "      { \"corp_name\": \"".$row['corp_name']."\" }";
		if ($j > 10) break;
	}
	print "\n";
	print "  ] \n";
//	print "} \n";

}
// 企業の入力欄
function inputCorp()
{
        $precorp=PRECORP;
        $preurl=PREURL;
        $pretag=PRETAG;
        $preaddress=PREADDRESS;

	$corpid="";
	$corp_name=$precorp;
	$url=$preurl;
	$corp_tag=$pretag;
	$address=$preaddress;
 

        print "    <table>\n";
        print "    <tr><th style=\"color:red;\">企業名 *(５文字以上必須)</th></tr><tr><td >\n";
//        print "    <input type=\"text\" name=\"corp_name\" maxlength=\"100\" style=\"color:#555;width:298px;\" disabled"
//                ." value=\"".$corp_name."\" onFocus=\"clearText(this);\" onClick=\"checkValue();\" onKeyup=\"checkValue();\" onBlur=\"setEx(this,'".$precorp."');\" />\n";
        print "    <input type=\"text\" name=\"corp_name\" maxlength=\"100\" size=50 disabled value=\"\""
		." placeholder=\"".$corp_name."\" onClick=\"checkValue();\" onKeyup=\"checkValue();\"  />\n";
        print "    </td></tr>\n";

        print "    <tr><th style=\"color:red;\">本社所在地 *(必須)</th></tr><tr><td  >\n";
        print "    <input type=\"text\" name=\"address\" maxlength=\"100\" disabled size=50 value=\"\" "
                ." placeholder=\"".$address."\" onClick=\"checkValue();\" onKeyup=\"checkValue();\" />\n";
        print "    </td></tr>\n";

        print "    <tr><th >URL</th></tr><tr><td >\n";
        print "    <input type=\"text\" name=\"url\" maxlength=\"100\" disabled size=50 "
                ." placeholder=\"".$url."\"  onClick=\"checkValue();\" onKeyup=\"checkValue();\" />\n";
        print "    </td></tr>\n";

        print "    <tr><th >キーワード ( , カンマ区切り)</th></tr><tr><td >\n";
        print "    <input type=\"text\" name=\"corp_tag\" maxlength=\"300\" disabled size=50 "
                ." placeholder=\"".$corp_tag."\" />\n";
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
	print "          <img border=\"0\" width=\"42\" height=\"42\" src=\"".$userimg."\" alt=\"口コミ ユーザ\" />";
	else
	print "          <img border=\"0\" width=\"42\" height=\"42\" src=\"images/mark_no.jpg\" alt=\"口コミ ユーザ\" />";

	print "        </div>\n";
	print "        <div class=\"tweet\">\n";
	print "          <div class=\"tweetTop\">\n";	

	$pretext = "(会社のクチコミ情報)";
	print "            <table>\n";
	print "              <tr><th>あなたはこの会社の、</th></tr><tr><td>\n";
	print "              <input id=\"usertype1\" type=\"radio\" name=\"user_type_id\" value=\"1\" ".$textdisable."/><label class=userType for=\"usertype1\">従業員</label>\n";
	print "              <input id=\"usertype2\" type=\"radio\" style=\"background:none; border:0px;\" name=\"user_type_id\" value=\"2\" ".$textdisable."/><label class=userType for=\"usertype2\">元従業員</label>\n";
	print "              <input id=\"usertype3\" type=\"radio\" style=\"background:none; border:0px;\" name=\"user_type_id\" value=\"3\" ".$textdisable."/><label class=userType for=\"usertype3\">取引先</label>\n";
	print "              <input id=\"usertype4\" type=\"radio\" style=\"background:none; border:0px;\" name=\"user_type_id\" value=\"0\" ".$textdisable." checked=\"checked\" /><label class=userType for=\"usertype4\">その他</label>\n";
	print "              </td></tr>\n";
        print "              <tr><th>評価</th><td><tr><td colspan=2>\n";
        print "              <input type=\"hidden\" name=\"user_star\" value=\"0\" />\n";
	if ($is_login)
	{
        print "              <a href=\"javascript:clickStar(1);\">"
                ."<img id=\"star1\" src=\"images/ico_star_off.gif\" width=\"19\" height=\"20\" alt=\"評判\" /></a>\n";
        print "              <a href=\"javascript:clickStar(2);\">"
                ."<img id=\"star2\" src=\"images/ico_star_off.gif\" width=\"19\" height=\"20\" alt=\"評判\" /></a>\n";
        print "              <a href=\"javascript:clickStar(3);\">"
                ."<img id=\"star3\" src=\"images/ico_star_off.gif\" width=\"19\" height=\"20\" alt=\"評判\" /></a>\n";
        print "              <a href=\"javascript:clickStar(4);\">"
                ."<img id=\"star4\" src=\"images/ico_star_off.gif\" width=\"19\" height=\"20\" alt=\"評判\" /></a>\n";

        print "              <a href=\"javascript:clickStar(5);\">"
                ."<img id=\"star5\" src=\"images/ico_star_off.gif\" width=\"19\" height=\"20\" alt=\"評判\" /></a>\n";
        print "              <span id=startext> </span></td></tr>\n";
	}
	else
	{
        print "    <img id=\"star1\" src=\"images/ico_star_off.gif\" width=\"19\" height=\"20\" alt=\"評判1\" />\n";
        print "    <img id=\"star2\" src=\"images/ico_star_off.gif\" width=\"19\" height=\"20\" alt=\"評判2\" />\n";
        print "    <img id=\"star3\" src=\"images/ico_star_off.gif\" width=\"19\" height=\"20\" alt=\"評判3\" />\n";
        print "    <img id=\"star4\" src=\"images/ico_star_off.gif\" width=\"19\" height=\"20\" alt=\"評判4\" />\n";
        print "    <img id=\"star5\" src=\"images/ico_star_off.gif\" width=\"19\" height=\"20\" alt=\"評判5\" />\n";
        print "    <span id=startext>-</span></td></tr>\n";
	}
        print "              <tr>\n";
        print "              <th >文字数(1-110): <span id=\"wordcounter\" style=\"color:red; font-size:16pt; font-weight:bold;\">0</span></th>\n";
        print "              </tr>\n";
        print "              <tr>\n";
	if ($is_login)
	{
        print "              <td ><textarea name=\"twitext\" rows=\"5\" cols=\"30\" onkeyup=\"document.frmtwi.twipost.disabled=false; wc(this.value);\" onclick=\"wc(this.value)\" placeholder=\"".$pretext."\" style=\"color:#555;width:300px;\" ></textarea></td></tr>\n";
        print "              <tr><td ><button name=\"twipost\" disabled onclick=\"this.disabled=true; valueCheckSubmit();\">";
        print "Twitterと同時投稿</button></td></tr>\n";
	print "              <tr><td><span style=\"color:#AAA;\">Tweetには自動で直リンクURLが付加されます</span></td></tr>\n";
	print "              <tr><th >YouTube動画URL　<a class=\"howtopaste\" href=\"images/howtoyoutube.jpg\">はりつけ方</a></th></tr>\n";
	$prevideo="(YouTubeから取得したURL)";
//        print "              <tr><td ><textarea name=\"videotag\" placeholder=\"".$prevideo."\" rows=\"3\" cols=\"30\" style=\"color:#555;width:300px;\" onFocus=\"clearText(this);\" ></textarea></td></tr>\n";
        print "              <tr><td ><input type=url name=\"videotag\" placeholder=\"".$prevideo."\" style=\"width:300px;\" maxlength=1000 /></td></tr>\n";
	}
	else {
        print "              <td ><textarea name=\"twitext\" rows=\"5\" cols=\"30\" onkeyup=\"document.frmtwi.twipost.disabled=false; wc(this.value);\" onclick=\"wc(this.value)\" disabled onFocus=\"clearText(this);\" style=\"color:#555;width:280px;\" >(TwitterIDでログイン後、投稿できます)</textarea></td></tr>\n";
	print "              <tr><th >YouTube動画タグ</th></tr>\n";
        print "              <tr><td ><textarea name=\"videotag\" rows=\"3\" cols=\"30\" disabled style=\"color:#555;width:280px;\"></textarea></td></tr>\n";
	}	
        print "            </table>\n";
	print "            <p class=\"tweetInfo\"></p>\n";
	print "          </div>\n"; //tweetTop
	print "          <!-- /TweetTop -->\n";
	print "          <div class=\"tweetBottom\"><!-- --></div>\n";
	print "        </div>\n";  //tweet
	print "        <!-- /Tweet -->\n";
	print "      <div class=\"clear\"><!-- --></div>\n";
	print "      </div>\n";    //tweetList
	print "      <!-- /TweetList -->\n";
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

		print "           <p>";
		print "           <img src=\"" .$row['imgfile']."\" border=0 alt=\"".$name."\" width=20 height=20 />\n";
		print "           <a href=\"http://twitter.com/".$row['screen_name']."\" target=\"_blank\" >";
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
function showTypeStar($typeid,$star)
{
	if ($typeid==1) print "Employee:";
	if ($typeid==2) print "OB/OG:";
	if ($typeid==3) print "Customer:";
//	if ($typeid==0) print "Others:";
	
	if ($star>=1) 
	{
        print"<img src=\"images/ico_star.gif\" width=\"10\" height=\"10\" alt=\"評判\" />";
//	else
      //  print"<img src=\"images/ico_star_off.gif\" width=\"10\" height=\"10\" alt=\"評判\" />";
	if ($star>=2) 
        print"<img src=\"images/ico_star.gif\" width=\"10\" height=\"10\" alt=\"評判\" />";
	else
        print"<img src=\"images/ico_star_off.gif\" width=\"10\" height=\"10\" alt=\"評判\" />";
	if ($star>=3) 
        print"<img src=\"images/ico_star.gif\" width=\"10\" height=\"10\" alt=\"評判\" />";
	else
        print"<img src=\"images/ico_star_off.gif\" width=\"10\" height=\"10\" alt=\"評判\" />";
	if ($star>=4) 
        print"<img src=\"images/ico_star.gif\" width=\"10\" height=\"10\" alt=\"評判\" />";
	else
        print"<img src=\"images/ico_star_off.gif\" width=\"10\" height=\"10\" alt=\"評判\" />";
	if ($star==5) 
        print"<img src=\"images/ico_star.gif\" width=\"10\" height=\"10\" alt=\"評判\" />";
	else
        print"<img src=\"images/ico_star_off.gif\" width=\"10\" height=\"10\" alt=\"評判\" />";
	}
}
function getAllCorp($rows,$lastcorp)  //１０企業を超えたら残りは企業名だけまとめて画面左下に表示
{
	$corplist="";
	$i=0;
        while($row = pg_fetch_assoc($rows))
	{
		if ($lastcorp!=$row['corp_id'])
		{
		$corplist.= "<a href=\"?c=".$row['corp_id']."\">".$row['corp_name'] ."</a>";
		$lastcorp=$row['corp_id'];
		$i++;
		if ($i > 20) break;
		}
	}
	return $corplist;
}
function addSearch($corp_id,$corp_name)  //TwitterとYahooblog検索のエリア
{
//Twitter検索
	print "      <!-- TweetList -->\n";
	print "      <div class=\"twitterList\">\n";
	print "        <div class=\"userIcon\">\n";
	print "          <img width=\"30\" height=\"30\" src=\""
		."http://a2.twimg.com/a/1310175040/images/logos/twitter_newbird_boxed_whiteonblue.png\""
		." alt=\"Twitter\" title=\"Twitter\" >\n";
	print "        </div>\n";
	print "        <div class=\"tweet\">\n";
	print "          <div class=\"tweetTop\">\n";	
	print "            <p><a href=\"javascript:showSearchTweet2('".$corp_id."');\">Twitter検索</a></p>";
        print "            <div id=\"twiblk_".$corp_id."\" style=\"display:none; text-align:left; \">\n";
	print "              <p><input id=\"twisct_".$corp_id."\" type=text size=30 maxlength=100 value=\"".trim_corp($corp_name)."\" /></p>\n";
	print "              <span class=\"twirst\"  id=\"twirst_".$corp_id."\"></span>\n";
	print "            </div>\n";
	print "            <!-- /twiblk_ -->\n";
	print "          </div>\n";
	print "          <!-- /Tweettop -->\n";
	print "          <div class=\"tweetBottom\"><!-- --></div>\n";
	print "        </div>\n";
	print "        <!-- /Tweet -->\n";
	print "        <div class=\"clear\"><!-- --></div>\n";
	print "      </div>\n";
	print "      <!-- /TweetList -->\n";

//Yahoo!検索
	print "      <!-- TweetList -->\n";
	print "      <div class=\"twitterList\">\n";
	print "        <div class=\"userIcon\">\n";
	print "          <img  width=\"27\" height=\"13\" src=\""
		."http://i.yimg.jp/images/login/btn/Ymark.gif\" "
//		."http://a2.twimg.com/a/1310175040/images/logos/twitter_newbird_boxed_whiteonblue.png\""
		." alt=\"Yahoo!\" title=\"Yahoo!\" >\n";
	print "        </div>\n";
	print "        <div class=\"tweet\">\n";
	print "          <div class=\"tweetTop\">\n";	
	print "            <p><a href=\"javascript:showSearchYahoo('yahoos_".$corp_id."','"
				.trim_corp($corp_name)."','ifyahoo_".$corp_id."');\"" 
			.">Yahoo! Blog検索</a></p>\n";
        print "            <div id=\"yahoos_".$corp_id."\" style=\"display:none; text-align:center;  margin:0 auto; border:none\">\n";
        print "              <iframe id=\"ifyahoo_".$corp_id."\" src=\"whitepage.html\" scrolling=\"yes\" frameborder=\"1\" style=\"overflow:visible; width:300px; height:300px; border:outset;\" ></iframe>\n";

	print "            </div>\n";
	print "            <!-- /yohoos_ -->\n";
	print "          </div>\n";
	print "          <!-- /tweettop -->\n";
	print "          <div class=\"tweetBottom\"><!-- --></div>\n";
	print "        </div>\n";
	print "        <!-- /tweet -->\n";
	print "        <div class=\"clear\"><!-- --></div>\n";
	print "      </div>\n";
	print "      <!-- /tweetlist -->\n";
/*
	print "      <div class=\"graphArea\">\n";
        print "          <iframe class=\"graphArea\" name=ifgraph id=\"g_".$corp_id."\" src=\"beforegraph.html\" scrolling=\"no\" frameborder=\"0\"  ></iframe>\n";
	print "      </div>\n";
	print "<script language=\"JavaScript\"><!-- \n";
	print "    var ifr".$i."= document.getElementById('g_".$corp_id."'); \n";
	print "   setTimeout( function() { \n";
	print "    ifr".$i.".src='corpgraph.php?c=".$corp_id."'; \n";
	print "   }, 5000); ";   //行ごとにグラフ更新を３秒間隔
	print "// --></script> \n";
*/
}
function searchVideos(){
	global $db;
	$select1 = "select b.status_id,b.corp_name, b.corp_id, b.rep_text,  b.update_date ,m.member_id,b.rep_id"
	.",m.screen_name,b.address,b.url ,m.imgfile as mimg ,b.corp_tag ,b.star,b.user_type_id,b.delete_flg,b.searchtext, b.videotag"
	." from corprep b"
	." left join members m on m.user_id=b.user_id"
	." left join (select corp_id,max(update_date) as udate from corprep "
	." where videotag != null group by corp_id ) bo "
	." on bo.corp_id=b.corp_id";
	
}
// 画面左側のクチコミ一覧
function searchEntries($db, $searchword = '', $user_id,$corp_id,$nowpage=1,$userimg=null,$member_id=0) {
	$addwhere = "";
	if (is_null($searchword)) $searchword="";
	if ($corp_id>0) $addwhere=" and b.corp_id = '".pgs($corp_id)."' ";
	else
	{
//  条件なしの検索時は最近のデータしかみせない
		if (($searchword=="")&&is_null($user_id))  $addwhere .= " and b.update_date > now() - interval'200 days'";
	}
//var_dump($addwhere);

	if (is_null($user_id))   //ユーザ 指定なし  (最新のクチコミ or 企業指定)
	{
		if ($member_id==ADMIN_MEMBER_ID) //管理者のときは全部、そうじゃないときは未削除のみ
			$delwhere .= " and (b.delete_flg!='2' )";
		else
			$delwhere .= " and (b.delete_flg!='1' )";

		$tabcounter=0;
		$npc="1";  //all
		if ($corp_id>0)
		{
	       		$select1 = "select b.status_id,b.corp_name, b.corp_id, b.rep_text,  b.update_date ,m.member_id,b.rep_id"
			.",m.screen_name,b.address,b.url ,m.imgfile as mimg ,b.corp_tag ,b.star,b.user_type_id,b.delete_flg,b.searchtext,b.videotag"
			." from corprep b"
			." left join members m on m.user_id=b.user_id"
//		." left join (select corp_id,max(update_date) as udate from corprep "
//		." where corp_name like '%".pgs($searchword)."%' group by corp_id ) bo "
//		." on bo.corp_id=b.corp_id"
			." where b.corp_id = '".pgs($corp_id)."'"
			.$delwhere
			." order by update_date desc";
		}
		else {
	       		$select1 = "select b.status_id,b.corp_name, b.corp_id, b.rep_text,  b.update_date ,m.member_id,b.rep_id"
			.",m.screen_name,b.address,b.url ,m.imgfile as mimg ,b.corp_tag ,b.star,b.user_type_id,b.delete_flg,b.searchtext, b.videotag"
			." from corprep b"
			." left join members m on m.user_id=b.user_id"
			." left join (select corp_id,max(update_date) as udate from corprep "
			." where corp_name like '%".pgs($searchword)."%' group by corp_id ) bo "
			." on bo.corp_id=b.corp_id"
			." where b.corp_name like '%".pgs($searchword)."%' "
//			." where b.corp_name like '%' "
			.$addwhere
			.$delwhere
			." order by bo.udate desc,b.update_date desc";
		}
	}
	else {   // (ログイン後の自分のクチコミを見るとき)
		$npc="2";  //my
	       	$tabcounter=10;
		$select1 = "select b.status_id,b.corp_name, b.corp_id, b.rep_text,  b.update_date ,m.member_id,b.rep_id"
		.",m.screen_name,b.address,b.url ,m.imgfile as mimg ,b.corp_tag ,b.star,b.user_type_id, b.delete_flg,b.searchtext, b.videotag"
		." from corprep b"
		." left join members m on m.user_id=b.user_id"
		." left join (select corp_id,max(update_date) as udate from corprep "
		." where user_id ='".pgs($user_id)."' group by corp_id ) bo "
		." on bo.corp_id=b.corp_id"
		." where b.user_id ='".pgs($user_id)."' "
		.$addwhere
		." order by bo.udate desc,bo.corp_id,b.update_date desc";
	}


//var_dump($select1);
        $rows = pg_query($db, $select1);
        $i=0;   //会社の数
        $j=0;   //データ行数
	$lastcorp=0;
	$lastcorpname="";
	$scrtext = "";
	$numrows=pg_num_rows($rows);
	$nextflg=true;
	$morerec=false;
	$corpname=null;
	$corplist="";
        while($row = pg_fetch_assoc($rows))
        {
		$j++;
//前の行と同じ会社かどうかの判断
		if ($lastcorp!=$row['corp_id']) {
			$corpname=$row['corp_name'];
			if (!$nextflg) print "      </div> <!-- tweet2 -->\n";  //tweet2のクローズ
//			if (($lastcorp!=0)&&($corp_id>0)) addSearch($lastcorp,$lastcorpname);

			$i++;
			$nextflg=true;
			$lastcorp=$row['corp_id'];
			$lastcorpname=$row['corp_name'];
			if ($i <= ($nowpage-1)*10) continue;  //10件までしか表示しない javascriptにも10で実装
			if ($i > 10+($nowpage-1)*10) //表示完了後は、残りの企業名だけまとめ表示
			{
				$i--; 
				$corplist=getAllCorp($rows,$lastcorp);
				$morerec=true;
				break;
			}  //10件までしか表示しない javascriptにも10で実装
			$tabcounter++;
	print "      <!-- corpInfo -->\n";
	print "      <div class=\"corpInfo\">\n";
	print "        <h3>" . $row['corp_name']. "\n";
// 企業選択
			if ($corp_id>0) {
//	print "        <a href=\"javascript:history.back()\">[もどる]　</a>\n";
	print "        <a href=\"?\">[もどる]　</a>\n";
			}
			else {
	print "        <a class='editicon' href=\"?c=" .$row['corp_id']."\">[書く]　</a>\n";
			}
	print "        <span  style=\"font-size:50%; color:#CCC;\">".$row['corp_id']."</span>\n";
	print "        </h3>\n";
//$hira=kataToHira($row['corp_name']);
//var_dump($hira);
// 本社所在
	print "      <table>\n";
	print "        <tr>\n";
	print "        <th>".  h($row['address'])."\n";
	print "        </th></tr>\n";

// URLがあるときだけリンク表示
			if (!is_null($row['url'])&&$row['url']!="")
			{
				$showurl=preg_replace("/https?:\/\//i","",$row['url']); //http...をとる
				$showurl=preg_replace("/\/.*$/i","",$showurl);  //    "/"以降をとる
//var_dump($showurl);
	print "        <tr><th><a href=\"" .$row['url']."\" target=_blank>".h($showurl)."</a>\n";
	print "        </th></tr>\n";

			}
// Keyword
			if (!n($row['corp_tag'])){
			if (mb_strlen($row['corp_tag'],'UTF-8') > 30)
			$tagtext = mb_substr($row['corp_tag'],0,28,'UTF-8')."...";
			else $tagtext=$row['corp_tag'];

	print "        <tr>";
//	print "        <td><span id=\"tag_log".$i."\" style=\"color:#00a;\">".
	print "        <th>".  h($tagtext)."\n";
	print "        </th></tr>\n";
			}
	print "      </table>\n";
// Graph
	print "      <div class=\"graphArea\">\n";
        print "          <iframe class=\"graphArea\" name=ifgraph id=\"g_".$row['corp_id']."\" src=\"beforegraph.html\" scrolling=\"no\" frameborder=\"0\"  ></iframe>\n";
	print "      </div>\n";
	print "<script language=\"JavaScript\"><!-- \n";
	print "    var ifr".$i."= document.getElementById('g_".$row['corp_id']."'); \n";
	print "   setTimeout( function() { \n";
	print "    ifr".$i.".src='corpgraph.php?c=".$row['corp_id']."'; \n";
	print "   }, ".($i*3)."000); ";   //行ごとにグラフ更新を３秒間隔
	print "// --></script> \n";
	print "      </div>\n";
	print "      <!-- /corpInfo -->\n";

			if ($corp_id> 0)   // corp_id があるときはTweetエリアいれる
			{  
addSearch($lastcorp,$lastcorpname);
	print "      <input type=\"hidden\" name=\"c\" value=\"".$corp_id."\"/>\n";
        print "      <input type=\"hidden\" name=\"corp_name\" value=\"".$row['corp_name']."\" />\n";
        print "      <input type=\"hidden\" name=\"address\" value=\"".$row['address']."\" />\n";
        print "      <input type=\"hidden\" name=\"url\" value=\"".$row['url']."\" />\n";
        print "      <input type=\"hidden\" name=\"corp_tag\" value=\"".$row['corp_tag']."\" />\n";
        print "      <input type=\"hidden\" name=\"searchtext\" value=\"".$row['searchtext']."\" />\n";
				inputTweet($userimg);
			}
		}
// 前の行と同じ企業の場合
		else {
			if (is_null($corp_id)&&($nextflg==true))  //企業コードの指定がないときだけ
			{
/*
	print "      <!-- TweetList -->\n";
	print "      <div class=\"twitterList\">\n";
	print "        <div class=\"userIcon\">\n";
	print "          <img  width=\"30\" height=\"30\" src=\"images/mark_no.jpg\" alt=\"クチコミ ユーザ\" />";
	print "        </div>\n";
	print "        <div class=\"tweet\">\n";
	print "          <div class=\"tweetTop\">\n";	
	print "            <p>さらに投稿があります。<a href=\"javascript:show2nd('tweet2nd_".$row['corp_id']."');\">すべて見る</a></p>\n";
	print "          <p class=\"tweetInfo\"></p>\n";
	print "          </div>\n";
	print "          <div class=\"tweetBottom\"><!-- --></div>\n";
	print "        </div>\n";
	print "      <div class=\"clear\"><!-- --></div>\n";
	print "      </div>\n";
	print "      <!-- /TweetList -->\n";
*/
	print "            <p class=\"others\" >他にも投稿があります。<a href=\"javascript:show2nd('tweet2nd_".$row['corp_id']."');\">すべて見る</a></p>\n";

        print "      <div id=\"tweet2nd_".$row['corp_id']."\" class=\"tweet2nd\">\n";
				$nextflg=false;  //ループを抜けたときにこのdivを閉じないといけないかを判断
			}
		}

	print "        <!-- TweetList -->\n";
	print "        <div class=\"twitterList\">\n";

// nowpage変数から画面表示対象の行かを判断
                if ($i > ($nowpage-1)*10) {
			$text=hsc_decode($row['rep_text']);

	print "          <div class=\"userIcon\">\n";
			if (!is_null($row['screen_name'])&&($row['screen_name']!=""))
			{
	print "            <a href=\"http://twitter.com/".$row['screen_name']
			."\" target=\"_blank\">\n";
	print "            <img  width=\"42\" height=\"42\" src=\""
			.$row['mimg']."\" alt=\"クチコミ ユーザ\" />";
	print "            </a>\n";
			}
			else
	print "            <img width=\"30\" height=\"30\" src=\"images/mark_no.jpg\" alt=\"クチコミ ユーザ\" />";

	print "          </div>\n";

// 削除データ処理
	$delclass="";
	$delicon="";
			if ((($member_id==$row['member_id'])||(ADMIN_MEMBER_ID==$member_id))&&($member_id > 0))
			{
				if ($row['delete_flg']=='1')  //削除済みなら１
				{
	$delicon= "<a href=\"javascript:modosu_tweet(".$row['rep_id'].");\"><img id=\"del_"
				.$row['rep_id']."\" src=\"images/modosu.jpg\" width=30 height=15 alt=\"戻す\"/></a>\n";
	$delclass ="del";
				}
				else {
	$delicon ="<a href=\"javascript:delete_tweet(".$row['rep_id'].");\"><img id=\"del_"
				.$row['rep_id']."\" src=\"images/delete.jpg\" width=30 height=15 alt=\"削除\"/></a>\n";
				}
			}
	print "          <div class=\"tweet\">\n";
	print "            <div class=\"tweetTop\">\n";
	print "              <p>" .url2Link(h($text))."\n";
	print "              </p>\n";
	print "            <p class=\"tweetInfo\">\n";
//   RTあんまり使わないのでお休み
//			if (($member_id!=$row['member_id'])&&($member_id > 0)&&($row['status_id']>0))
//	print "              <a href=\"javascript:reTweet('".$row['corp_id']."','".$row['status_id']."');\">RT</a>";
	print "            ".$delicon;

			if ($delclass!="del") {  
			//削除済み以外
				if ($row['status_id']>0) {  
				// twitterへの直リンク表示
	print "              <a href=\"http://twitter.com/".$row['screen_name']."/statuses/"
			 .$row['status_id']."\" target=\"_blank\" title=\"Link to Twitter\">\n";
	print "              <img src=\"http://twitter-badges.s3.amazonaws.com/t_mini-b.png\" alt=\"twitter\"/></a> ";
				}
				  // 星、ユーザタイプ、更新時刻
	print "          ".showTypeStar($row['user_type_id'],$row['star']).h($row['screen_name'])
			." - ".date('m/d H:i:s',strtotime($row['update_date']))."</p>\n";
			}
			else
	print "            <span style=\"font-size:80%;\">この投稿は削除されてほかのユーザからは見えない状態です。</span>";

		
			if (!n($row['videotag'])){
	print "          <p class=\"videotag\" >".hsc_decode($row['videotag'])."</p>\n";
			}
	print "            </div>\n";
	print "            <!-- /TweetTop -->\n";
	print "            <div class=\"tweetBottom\"><!-- --></div>\n";
	print "          </div>\n"; //Tweet
	print "          <!-- /Tweet -->\n";
	print "          <div class=\"clear\"><!-- --></div>\n";
	print "        </div>\n"; //TwitterList
	print "        <!-- /TweetList -->\n";
		}
        }
	if (!$nextflg) 
	print "      </div> <!-- /tweet2 -->\n";  //tweet2のクローズ


//	if (($lastcorp!=0)&&(!$morerec)&&($corp_id>0)) addSearch($lastcorp,$lastcorpname);


	if (!($corp_id>0)) //corpidがあるときは一番下に新規登録画面をださない
	 // corpIDがないときだけ  件数とページ切り替えつける
	{
	print "      <div class=\"corpInfo\" style=\"color:#aaa;\">\n";
//var_dump($searchword);
       		if ($nowpage > 1) 	
	print "        <a href=\"?searchword=".urlencode($searchword)."&nowpage".$npc."="
			.($nowpage-1)."\" >Back</a>\n";
		if ($morerec)  //10件までしか表示しない javascriptにも10で実装
		{
//	print "        <a href=\"?searchword=".urlencode($searchword)."&nowpage".$npc."="
//			.($nowpage+1)."\" >Next</a>\n";
	print "        <p>他にこれらの企業についても投稿されています<br>";
	print $corplist."</p>\n" ;
		}
//	print "        <p>(keyword=[ ".$searchword." ] ".($i)."件)</p>\n";
	print "        <p> </p>\n";
	print "        <br />\n";
	print "        <p style=\"font-size:160%;\"><a href=\"javascript:editCorp();\" >新規企業登録+投稿</a></p>\n";
	print "      </div>\n"; //corpInfo
	print "      <!-- /corpInfo -->\n";

        print "      <div id=\"newcorp\" style=\"display:none;\">\n";
	print "        <div class=\"corpInfo\"  >\n";
        print "          <input type=\"hidden\" name=\"corp_id\" value=\"".$corp_id."\"/>\n";
	print "            ".inputCorp()."\n";
	print "        </div>\n";  //corpInfo
	print "            ".inputTweet($userimg)."\n";
	print "      </div>\n";    //newcorp
	print "      <!-- /newCorp -->\n";
 
	}
	
	return $numrows;
}

function reTwitter($to, $sid,&$twitext) //retweet機能
{
	$req = $to->OAuthRequest("https://api.twitter.com/1/statuses/retweet/".$sid.".xml",
		"POST",array("trim_user"=>"true"));
        $xml = simplexml_load_string($req);
//var_dump($xml);

	$created_at =$xml->created_at; // 標準時 9 hours late
        $status_id = $xml->id;  // 呟きのステータスID
        $text = $xml->text;  // @つきのテキスト
        $rtext = $xml->retweeted_status->text;  //もとのテキスト
	$twitext=$text;

        if ($status_id>0) print "<table border=\"1\"><tr><td>".h($text)."</td></tr></table>\n";
        else print "<br><font color=#FF3333>ReTweet ERROR</font><br>";

	return $status_id;

}
function postToTwitter($message,$to,$replyid=null){ // 通常のTwitter投稿
	$len0=mb_strlen($message,'UTF-8');

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
        if (is_null($replyid)) {
		$req = $to->OAuthRequest("https://api.twitter.com/1/statuses/update.xml",
		 "POST",array("status"=>$message));
        }
        else {
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
