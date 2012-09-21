<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta name="keywords" content="deecorp,ディーコープ,企業,会社一覧,口コミ,評判,噂,クチコミ,会社リスト" />
<meta name="description" content="ディーコープが提供する企業クチコミ情報。あなたの会社や取引先についてTwitterユーザがどんな評価をしているか、どんな評判・口コミ情報があるか探してみてください。" />

<title>blog検索</title>

</head>
<body>
 

<?php
ob_start();

require_once("corpheader.php");
require_once("corppara.php");
require_once("corpfnc.php");

// corptwi.php   corpgraph.phpから。　TwitterとYahoo blog検索結果をDBに保存

define ('BADWORDS','(悪い|ひどい|面白くない|楽しくない|うれしくない|遅い|残念|BAD|批判|汚い|美しくない|かっこわるい|イマイチ)');
define ('BADWORDS2','(最悪|嘘|謝罪|炎上|駄目|酷い|虚偽|不適切|架空|撤退|断念)');

define ('GOODWORDS','(良い|すばらしい|面白い|楽しい|うれしい|速い|GOOD|賞賛|かっこいい|美しい|綺麗|優秀)');
define ('GOODWORDS2','(最高|おいしい|旨い|凄い|好評|高い?評価|真実|進出|継続)');


function yahooKakari($sentence,$keyword)
{
	$sentence=urlRemove($sentence);
	$sentence=atRemove($sentence);

//var_dump($sentence);
        $request  = "http://jlp.yahooapis.jp/DAService/V1/parse?"
        	. "appid=".YAHOO_ID."&sentence=".urlencode($sentence);
	$responsexml=httpRequest($request);
	if (is_null($responsexml)) print "キーフレーズがありません<br>";

	if (is_null($responsexml->Result)) $result_num=0;
	else $result_num = count($responsexml->Result);
//var_dump($responsexml->Result);

	$keystrs=array();
		print "<table border=0>\n";
		echo "<tr bgcolor=#EEEEEE><th>キーフレーズ</th></tr>\n";

		print "<tr>\n";
	$result = $responsexml->Result;
	$dep="";
	if (true)
	{
//var_dump($result);
		foreach($result->ChunkList as $cl)
		{
			
		print "<td>\n";
//var_dump($cl);
			foreach($cl->Chunk as $ck)
			{
			$ckid=$ck->Id;
			$dependency=$ck->Dependency;
//var_dump($ckid);
//var_dump($dependncy);
			foreach($ck->MorphemList as $ml)
			{
			foreach($ml->Morphem as $mm)
			{
//var_dump($ml);
			if (preg_match("/".$keyword."/i", $mm->Surface))
			{
				print "★".$ckid." ".$mm->Surface." ".$dependency;
				$dep=$dependency;
			}
			if ($dep==$ckid)
			{
				print "*".$ckid.":".$dep." ".$mm->Surface." ";
			}

			}
			}
			}
		print "</td>\n";
		}

	}              
		print "</tr>\n";
                print "</table>\n";
	return $keystrs;
}

function yahooKeyphrase($sentence)
{
        $output = "xml";
//var_dump($sentence);
	$sentence=urlRemove($sentence);
	$sentence=atRemove($sentence);
        $request  = "http://jlp.yahooapis.jp/KeyphraseService/V1/extract?";
        $request .= "appid=".YAHOO_ID."&sentence=".urlencode($sentence)."&output=".$output;
	$responsexml=httpRequest($request);
	if (is_null($responsexml)) print "キーフレーズがありません<br>";

	if (is_null($responsexml->Result)) $result_num=0;
	else $result_num = count($responsexml->Result);

	$keystrs=array();
	if($result_num > 0){
		print "<table border=0>\n";
		echo "<tr bgcolor=#EEEEEE><th>キーフレーズ</th><th>スコア</th></tr>\n";

		print "<tr>\n";
		for($i = 0; $i < $result_num; $i++){
                        $result = $responsexml->Result[$i];
                        $keystrs[] = $result->Keyphrase;
                        print "<td>".hsc($result->Keyphrase)."</td><td>".
                                hsc($result->Score)."</td>";
                        if ($i > 10) break;
                }
		print "</tr>\n";
                print "</table>\n";
	}
	return $keystrs;
}
function getLastSearch($db, $cid)
{
	$searchok=true;
        $select1 = "select max(search_date) as searchdate,max(update_date) as updatedate from keytext "
        ." where corp_id = '".pgs($cid)."'";
	
        $rows = pg_query($db, $select1);

        while($row = pg_fetch_assoc($rows))
        {
                $i++;
                $corpname=$row['corp_name'];
                $lastsearch=$row['searchdate'];
                $lastupdate=$row['updatedate'];
	print "date:".time()."<br />\n";
	print "search:".strtotime($lastsearch)."<br />\n";
	print "update:".strtotime($lastupdate)."<br />\n";
	print "[seconds from last search]: ".(time()-strtotime($lastsearch))."<br />\n";
	print "[seconds from last update]: ". (strtotime($lastsearch)-strtotime($lastupdate));
		if (time()-strtotime($lastsearch) < strtotime($lastsearch)-strtotime($lastupdate)) $searchok=false;
//var_dump($searchok);
		if (time()-strtotime($lastsearch)>3600) $searchok=true;
//var_dump($searchok);
		if (time()-strtotime($lastsearch)<60) $searchok=false;
//var_dump($searchok);
	}
	return $searchok;
	
}
function saveText($db,$keyword,$corpid=0,$text,$datetime,$sid, $source,$username,$pts)
{
//	$textid=md5($username.$text);
	$goods=0; $bads=0;
	if ($pts > 0) $goods=$pts;
	if ($pts < 0) $bads=-$pts;
	$select1="update keytext set keywords='".pgs($keyword)."',corp_id=".$corpid.", keytext= '".pgs($text)
		."',username='".pgs($username)."', search_date=now(), update_date='".$datetime."' "
		. ",goods=".$goods.", bads=".$bads." "
		." where text_id='".$corpid."/".$sid ."'";
//var_dump($select1);
        $rows = pg_query($db,$select1);
        if (pg_affected_rows($rows)==1) print "u";
	else 
	{

	$select1="insert into keytext(keywords,corp_id,keytext,update_date,text_id,source,search_date"
		." ,username ,goods,bads)  values('"
		.pgs($keyword)."',".$corpid.", '".pgs($text)."', '".$datetime."','".$corpid."/".$sid.
		 "','".pgs($source)."',now(),'".pgs($username)."' ,".$goods.",".$bads.")";
        $rows = pg_query($db,$select1);
//var_dump($rows);
        if (pg_affected_rows($rows)==1)
        {
                printf("i");
        }
        else  print"*";
	}

}

function twiSearch($db,$sentence,$cid,$nowpage){
	
	if (!($cid > 0)) $cid=0;
	if (!($nowpage>0)) $nowpage=1;;
        $keyword=urlencode($sentence);
        $request = "http://search.twitter.com/search.atom?page=".$nowpage."&rpp=20&lang=ja&q=".$keyword;

	$responsexml=httpRequest($request);
//var_dump($responsexml);
	$recs=0;
	if (is_null($responsexml)) print "No response<br>";
	print "<table width=600>";
	$alltitle="";
	foreach($responsexml->entry as $entry){
		print "<tr><td width=60%>\n";
		$published =$entry->published;

//var_dump($entry);
		$id = $entry->id;  // 呟きのステータスID
		$title = $entry->title;  // 呟き

		$uri= $entry->author->uri;  // 直リンク
		$updated = $entry->updated;
		$screen_name = $entry->author->name;  // 名前
		$href = $entry->link[0]->attributes()->href;  //直 リンク
		$uri = $entry->author->uri;  //USER リンク
		$img=$entry->link[1]->attributes()->href;   //profile image

		print "<img src=\"".$img."\" width=30 height=30/>".$title."<br />";
		$recs++;
		print "</td><td>\n";
	//	yahooKakari($title,$sentence);
//		yahooKeyphrase($title);
		$pts=wordPoint($title);
		$alltitle .= " ".$title;
		print " Updated:".$updated;
		print " Point:".$pts;
		print "</td></tr>\n";
//var_dump($entry->author)i;
		saveText($db,$sentence,$cid, $title, $updated,$id,"twitter",$uri,$pts);
	}
	print "</table>";
//	yahooKeyphrase($alltitle);
	return $recs;
}

function wordPoint($str)
{
	$pts=0;
	if (preg_match("/".GOODWORDS."/ui", $str)) $pts++;
	if (preg_match("/".GOODWORDS2."/ui", $str)) $pts++;
	if (preg_match("/".BADWORDS."/ui", $str)) $pts--;
	if (preg_match("/".BADWORDS2."/ui", $str)) $pts--;
	return $pts;
}
function blogSearch($db,$yahoo_id, $sentence,$cid,$nowpage=1){
	if (!($cid > 0)) $cid=0;
	if (!($nowpage>0)) $nowpage=1;;
        $output = "xml";
        $request  = "http://search.yahooapis.jp/BlogSearchService/V1/blogSearch?";
        $request .= "appid=".$yahoo_id."&format=html&results=20&start=".(($nowpage-1)*20+1).
		"&query=".urlencode($sentence);
	$responsexml=httpRequest($request);

//var_dump($responsexml);

	if (is_null($responsexml->Result)) $result_num=0;
                else $result_num = count($responsexml->Result);

	$rescount = $responsexml->attributes()->totalResultsAvailable;
//var_dump($rescount);
//var_dump($result_num);
	$maxpage=$nowpage+9;
	if ($maxpage*20 > $rescount)  $maxpage=ceil($rescount/20);
	if (($rescount==0)||($result_num==0)) print "検索結果 0件";
	else
	{
		
		for ($i=1;$i <= $maxpage; $i++)
		{
		if ($i==$nowpage)
			print " ".$i . " \n";
		else
		
			print " <a href=\"javascript:document.frm0.nowpage1.value=".
				$i."; document.frm0.submit();\">"
				. $i."</a> \n";
		
		}

                $keystrs=array();
		$url="";
                echo "<table border=1 width=600>";
                echo "<tr bgcolor=#EEEEEE ><th>検索結果</th></tr>";

                for($i = 0; $i < $result_num; $i++){
                	$result = $responsexml->Result[$i];

			if (mb_strlen($result->Url,'UTF-8')>45) 
				$url=mb_substr($result->Url,0,42,'UTF-8')."..."; 
			else $url=$result->Url;
			$title=$result->Title;
			$desc=$result->Description;
			$updated=$result->DateTime;

			$pts=0;
			$pts=wordPoint($title.$desc);
                       	print "<tr><td><a href=\"". trim($result->Url)."\" target=_blank>"
			.str_replace($sentence,"<strong>".$sentence."</strong>" , $title)."</a><br />"
			.str_replace($sentence,"<strong>".$sentence."</strong>" , $desc)."<br />".trim($url);
			print " ".$updated." ";
			print " Point:".$pts;
			print "</td></tr>\n";
			$sid=md5($desc);
			saveText($db,$sentence,$cid, $title."/".$desc, $updated,$sid,"yahooblog",$reult->Url,$pts);
                }
                print "</table>";
	}
	return $updateurls;
}
function getSearchText($db,$cid,$searchtext,&$yahootext,&$twitext)
{
	$searchtext="";
	if ($cid > 0)
	{
        $select1 = "select corp_tag ,corp_name, searchtext from corprep "
        ." where corp_id = '".pgs($cid)."' order by update_date desc";
	
        $rows = pg_query($db, $select1);

        while($row = pg_fetch_assoc($rows))
        {
                $i++;
                $corpname=$row['corp_name'];
                $corptag=$row['corp_tag'];
 //               $searchtext=$row['searchtext'];
		break;
        }
//	$searchtext=preg_replace("/^[a-zA-Z0-9\.\/\?=:%,!#~*@&_\-]+/","",$searchtext);
//	$searchtext = trim($searchtext);
var_dump($searchtext);
	$searchtext= str_replace(" ","",$corptag);
	$searchtext=str_replace("　","",$searchtext);
	$yahootext = "(" .$searchtext.")";

	$yahootext = str_replace(","," ",$yahootext);
	$yahootext = str_replace("、"," ",$yahootext);

	$twitext=str_replace(","," OR ",$searchtext);
	$twitext=str_replace("、"," OR ",$twitext);

	$twitext = trim($twitext);
	print "TWITEXT:".$twitext;	
	print "<br />YAHOOTEXT:".$yahootext;	
	}
	return $searchtext;
}

//	if (isset($_POST['searchword'])) $searchword = trim($_POST['searchword']);
//	if (isset($_GET['searchword'])) $searchword = trim($_GET['searchword']);
	$search0='';
	$ym=array();

	$yahootext="";
	$twitext="";
	$searchword=getSearchtext($db,$corpid,$searchword,$yahootext,$twitext);

	$searchword=trim($searchword." ".$search0);

//var_dump($nowpage1);
	print "<form method=\"get\" name=\"frm0\" action=\"?\">";
//	print "<input type=\"text\" name=\"searchword\" size=50 maxlength=300 value=\"".$searchword."\" />";
	print "<input type=\"hidden\" name=\"c\" value=\"".$corpid."\" />\n";
	print "<input type=\"submit\" name=\"searchpost\" value=\"Next\" />\n";
	print "<input type=\"hidden\" name=\"nowpage1\" value=\"".($nowpage1+1)."\" />\n";
//var_dump($searchword);

	$searchok=true;
	if ($corpid>0) $searchok=getLastSearch($db, $corpid);

	if ((!is_null($searchword))&&($searchword!="")&&($searchok))
	{
	print "<h2>Yahoo! BLOG検索</h2>";
	$rec_count=0;
//	$nowapge=1;
	$scripttext="";
	$rec_count=blogSearch($db,$yahoo_id,$yahootext,$corpid,$nowpage1);
	print "<h2>Twitter検索</h2>";
	$rec_count=twiSearch($db,$twitext,$corpid,$nowpage1);
	}
	else print "<br \>検索結果なし\n";

	print "</form>";


?>

<!-- #totalWrapper -->
</body>
</html>

<?php
 pg_close($db);

$content=ob_get_contents();
ob_end_clean();
echo $content;

?>

