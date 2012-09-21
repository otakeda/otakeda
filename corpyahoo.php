<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta name="keywords" content="deecorp,ディーコープ,企業,会社一覧,口コミ,評判,噂,クチコミ,会社リスト" />
<meta name="description" content="ディーコープが提供する企業クチコミ情報。あなたの会社や取引先についてTwitterユーザがどんな評価をしているか、どんな評判・口コミ情報があるか探してみてください。" />

<title>blog検索</title>
<script src="corpscript.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/default.css" type="text/css" media="all" />
<link rel="stylesheet" href="css/common.css" type="text/css" media="all" />

</head>
<body>
 

<?php
ob_start();

require_once("corpheader.php");
require_once("corppara.php");
require_once("corpfnc.php");

/*
function showYahooSearch($db,$yahoo_id, $sentence,$nowpage,&$scripttext){
        $request  = "http://search.yahooapis.jp/WebSearchService/V2/webSearch?";
        $request .= "appid=".$yahoo_id."&format=html&results=20&start=".(($nowpage-1)*20+1).
		"&query=".urlencode($sentence);

if (SERVENV=="hon")
{
//var_dump("hon");
        $xml_string=file_get_contents($request, false);
        $responsexml=simplexml_load_string($xml_string);
}
else {
//var_dump("stage");
        $proxy_opts = array(
        'http' => array(
        'proxy' => 'tcp://svsns20:3128',
        ),
        );
        $proxy_context=stream_context_create($proxy_opts);
        $xml_string=file_get_contents($request, false,$proxy_context);
        $responsexml=simplexml_load_string($xml_string);
}
//var_dump($responsexml);
	if (is_null($responsexml)) print "No response<br>";

	if (is_null($responsexml->Result)) $result_num=0;
		else $result_num = count($responsexml->Result);
	$rescount = $responsexml->ResultSet->totalResultsAvailable;
	$maxpage=$nowpage+9;

	for ($i=1;$i <= $maxpage; $i++) {
		if ($i==$nowpage)
			print " ".$i . " \n";
		else
			print " <a href=\"javascript:document.frm0.nowpage1.value=".
			$i."; document.frm0.submit();\">" . $i."</a> \n";
	}

        $keystrs=array();
	$url="";
        if($result_num > 0){
       		echo "<table border=1 width=280>";
		echo "<tr bgcolor=#EEEEEE ><th>検索結果</th></tr>";
		for($i = 0; $i < $result_num; $i++){
                       	$result = $responsexml->Result[$i];
			if (mb_strlen($result->Url,'UTF-8')>45) 
				$url=mb_substr($result->Url,0,42,'UTF-8')."..."; 
			else $url=$result->Url;

                       	print "<tr><td><a href=\"". trim($result->Url)."\" target=_blank>"
			.mb_substr($result->Title,0,25,'UTF-8')."</a><br />".
			$result->Description."<br />".trim($url);
			print "</td></tr>\n";
		}
		echo "</table>";
	}
	return $updateurls;
}
*/

function showBlogSearch($db,$yahoo_id, $sentence,$nowpage,&$scripttext){
        $request  = "http://search.yahooapis.jp/BlogSearchService/V1/blogSearch?";
        $request .= "appid=".$yahoo_id."&format=html&results=20&start=".(($nowpage-1)*20+1).
		"&query=".urlencode($sentence);

if (SERVENV=="hon")
{
        $xml_string=file_get_contents($request, false);
        $responsexml=simplexml_load_string($xml_string);
}
else
{
//var_dump("stage");
        $proxy_opts = array(
        'http' => array(
        'proxy' => 'tcp://svsns20:3128',
        ),
        );
        $proxy_context=stream_context_create($proxy_opts);
        $xml_string=file_get_contents($request, false,$proxy_context);
        $responsexml=simplexml_load_string($xml_string);
}



//var_dump($request);
	if (is_null($responsexml)) print "No response<br>";

	if (is_null($responsexml->Result)) $result_num=0;
		else $result_num = count($responsexml->Result);

	$rescount = $responsexml->attributes()->totalResultsAvailable;
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
                echo "<table border=1 width=280>";
                echo "<tr bgcolor=#EEEEEE ><th>検索結果</th></tr>";

                for($i = 0; $i < $result_num; $i++){
                	$result = $responsexml->Result[$i];

			if (mb_strlen($result->Url,'UTF-8')>45) 
				$url=mb_substr($result->Url,0,42,'UTF-8')."..."; 
			else $url=$result->Url;

                       	print "<tr><td><a href=\"". trim($result->Url)."\" target=_blank>"
			.str_replace($sentence,"<strong>".$sentence."</strong>" , $result->Title)."</a><br />"
			.str_replace($sentence,"<strong>".$sentence."</strong>" , $result->Description)."<br />".trim($url);
//			.mb_substr($result->Title,0,25,'UTF-8')."</a><br />".
			print "</td></tr>\n";
                }
                print "</table>";
	}
	return $updateurls;
}

	if (isset($_POST['searchword'])) $searchword = trim($_POST['searchword']);
	if (isset($_GET['searchword'])) $searchword = trim($_GET['searchword']);
	$search0='';
	
	$searchword=trim($searchword." ".$search0);

//var_dump($nowpage1);
	print "<form method=\"get\" name=\"frm0\" action=\"?\">";
	print "<input type=\"text\" name=\"searchword\" size=20 maxlength=100 value=\"".$searchword."\" />";
	print "<input type=\"submit\" name=\"searchpost\" value=\"Search\" />\n";
	print "<input type=\"hidden\" name=\"nowpage1\" value=\"".$nowpage1."\" />\n";
//var_dump($searchword);
 	print "<div id=\"tweet\">\n";
	if ((!is_null($searchword))&&($searchword!=""))
	{
	$rec_count=0;
	$scripttext="";
	$rec_count=showBlogSearch($db,$yahoo_id,$searchword,$nowpage1,$scripttext);
	}
	else print "検索結果なし\n";
 	print "</div>\n";

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

