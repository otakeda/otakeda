<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta name="keywords" content="deecorp,ディーコープ,企業,会社一覧,口コミ,評判,噂,クチコミ,会社リスト" />
<meta name="description" content="graph api 会社クチコミ" />

<title>dee-graph</title>
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
require_once("corpauth.php");
define ("HOURS","6");
define ("DAYS","7");
define ("MONTHS","6");

function getNew($cid,$nowpage=1){

	if (!($nowpage > 0)) $nowpage=1;
        $request = "http://".$_SERVER['SERVER_NAME']."/corptwi.php?c=".$cid."&nowpage1=".$nowpage;

        $response=httpRequest0($request);
//var_dump($responsexml);
        if (is_null($response)) print "*";
	else ".";
 
}
function getSearched($db,$cid, $keyword,$term,$x,&$y,&$ystr, $maxi,$df)
{
	if ($cid > 0)
        $select1 = "select date_trunc('".$term."', update_date) as ymdhm , count(*) as cnt ,"
		."sum(goods) as goods, sum(bads) as bads from keytext "
        	." where corp_id ='".pgs($cid)."' group by  ymdhm order by ymdhm desc";
	else
        $select1 = "select date_trunc('".$term."', update_date) as ymdhm , count(*) as cnt,"
		."sum(goods) as goods ,sum(bads) as bads from keytext "
     		." where keywords like '%".pgs($keyword)."%' group by  ymdhm order by ymdhm desc";
	
        $rows = pg_query($db, $select1);

	$max_y=0;
	$y_good=array();
	$y_bad=array();
	for ($i=0;$i < $maxi;$i++) { $y[$i]=0; $y_good[$i]=0; $y_good[$i]=0; $y_bad[$i]=0;}
	$recs=0;
        while($row = pg_fetch_assoc($rows))
        {
		$recs++;
                $x0=$row['ymdhm'];
                $y0=$row['cnt'];
//print "x=".date('d/H', strtotime($x0)).":y=".$y0."<br />\n";
		for ($i=0;$i < $maxi;$i++)
		{
			if (date($df, strtotime($x0))==$x[$i])
			{
				$y[$i]=$y0; //y軸設定
				$y_good[$i]=$row['goods'];
				$y_bad[$i]=$row['bads'];
			}
//				$y_good[$i]=20;
//				$y_bad[$i]=10;
		}
        }
//var_dump($i);
//	$ystr="";
	for ($i=0;$i < $maxi;$i++) 
	{
		if ($i==0) 
		{
			$ystr.=$y[$i]; 
			$y_good_str.=$y_good[$i]; 
			$y_bad_str.=$y_bad[$i]; 
		}
		else 
		{
			$ystr.=",".$y[$i];
			$y_good_str.=",".$y_good[$i]; 
			$y_bad_str.=",".$y_bad[$i]; 
		}
//print $x[$i]."|".$y[$i]."<br />\n";
		if ($max_y <$y[$i]) $max_y=$y[$i];
	}
	$ystr.="|".$y_good_str."|".$y_bad_str;
//var_dump($ystr);
	return $max_y;   //  縦軸の設定のためyの最大値
}
function assembledUri($parts)
{
	$uri="http://chart.apis.google.com/chart?";
	$query="";
	foreach($parts as $key => $val)
	{
		if ($query!='') $query.="&amp;";
		$query .= "$key=$val";
	}
	$uri .= $query;
	return $uri;
}

	
	$member_id=null;
        try{
        $member_id= twiAuthCheck($db);
//var_dump($member_id);
        }catch(Exception $e)
        {
                print "<p>TwitterAPIアクセスエラー: <a href=\"?logoff=clear\">もう一度</a><br />"
		."一部ブラウザでログイン後の動作が不安定な場合があります。繰り返しエラー が出る場合、別のブラウザを使ってみてください</p>\n";
        }
        if ($member_id > 0) $is_login=true;

	if (($corpid>0)&&($is_login)) getNew($corpid,$nowpage); //最新データの取得
	else print "*";

	$x=array();
	$y=array();

	$x1str="0:";
	$x2str="1:";
	$lastx2="";
	

	$maxi=HOURS;  $x1t="時"; $x2t="日"; $df="Y:m:d/H"; $df1="H"; $df2="d";
	if ($term=="days") { $maxi=DAYS;  $x1t="日"; $x2t="月"; $df="y:m:d"; $df1="d"; $df2="m";}
	if ($term=="months") { $maxi=MONTHS;  $x1t="月"; $x2t="年"; $df="Y:m"; $df1="m"; $df2="Y";}

	for ($i=0;$i < $maxi;$i++)
	{
//	$x[$i]= date('Y/m/d', strtotime('-'.$i.' week'));
		$x[$i]= date('d/H', strtotime('-'.($maxi-1-$i).' hour'));
		$x[$i]= date($df, strtotime('-'.($maxi-1-$i).' '.$term));
		$x1[$i]= date($df1, strtotime('-'.($maxi-1-$i).' '.$term)).$x1t;
		$x2[$i]= date($df2, strtotime('-'.($maxi-1-$i).' '.$term)).$x2t;
		$x1str.= "|".$x1[$i];
		if ($x2[$i] != $lastx2) $x2str.= "|".$x2[$i];
		$lastx2=$x2[$i];
	}

	$ystr="t:";
	$max_y=0;
	if (((!is_null($searchword))&&($searchword!=""))||($corpid>0))
	{
		$max_y=getSearched($db,$corpid, $searchword,$term,$x,$y,$ystr,$maxi,$df);
	}
	else print "NO Keyword\n";

	print "Keyword出現回数<br />\n";

	if ($max_y<0) print "データがありません".$corpid;

//var_dump($max_y);
	if ($max_y < 30) $max_y=30;
	else $max_y=ceil($max_y /100) * 100;

	$label=$x1str."|".$x2str."|2:||".($max_y/2)."|".$max_y;

//var_dump($label);
	$label=rawurlencode($label);

	$parts=array(
		'cht' => 'lc',  //折れ線
		'chs' => '250x150', // size
		'chd' => $ystr,
		'chdl' => 'Total|Good|Bad',
		'chds' => '0,'.$max_y,
		'chxl' => $label,
		'chco' => '000000,ff0000,0000ff',  //y軸の色
		'chxt' => 'x,x,y',
		'chls' => '4,4,0|2,4,4|2,3,3'  //太さ,線あり長さ,線なし長さ
	);
	$graph=assembledUri($parts);

	print "<img src=\"".$graph."\" alt=\"graph\" />\n";

	print "<form method=\"get\" name=\"frm0\" action=\"?\">";
	if ($corpid> 0) 
	{
	print "<input type=\"hidden\" name=\"c\" value=\"".$corpid."\" />\n";
//	print "corp_id=".$corpid;
	}
	else
	print "<input type=\"text\" name=\"searchword\" size=30 maxlength=300 value=\"".$searchword."\" />";
	print "<input type=\"hidden\" name=\"nowpage1\" value=\"".($nowpage1+1)."\" />\n";

//var_dump($searchword);
//	print "<br />\n";
	if ($term=="hours")
	print "<input type=\"radio\" name=\"term\" value=\"hours\" checked=\"checked\"/>Hour\n";
	else
	print "<input type=\"radio\" name=\"term\" value=\"hours\" />Hour\n";
	if ($term=="days")
	print "<input type=\"radio\" name=\"term\" value=\"days\" checked=\"checked\"/>Day\n";
	else
	print "<input type=\"radio\" name=\"term\" value=\"days\" />Day\n";
	if ($term=="months")
	print "<input type=\"radio\" name=\"term\" value=\"months\" checked=\"checked\"/>Month\n";
	else
	print "<input type=\"radio\" name=\"term\" value=\"months\" />Month\n";

	print "<input type=\"submit\" name=\"searchpost\" value=\"Refresh\" />\n";
	print "</form>";
/*
<script language="JavaScript"><!-- 
	setTimeout( 'document.frm0.submit()', 60000); 
// --></script>
*/
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

