<?php
/**
 *   function定義
 **/


//  HTTPでリクエスト送信
//  $request : HTTP requestのURL文字列
//  return : XML形式でのレスポンス文字列
// XML形式で返す
function httpRequest($request)
{
        $responsexml =null;
        if (SERVENV=="hon")
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
//  2階層目のカテゴリを取得して表示
//  $req : HTTP requestのURL文字列
function get2ndCategory($req)
{
//var_dump($req);
	$xml=httpRequest($req);
	print "    <ul>\n";
	print "      <li  class=\"spt\"><p>案件名</p><span class=\"spc\">見積提出期限</span><span class=\"spr\">詳細</span></li>\n";
	foreach($xml->channel->item as $item)
	{
		$linkurl=preg_replace("/Rss.action/i","Index.action",$item->link);
//var_dump($linkurl);
		if (preg_match('/\[入札開催日:/',$item->title))
		{
		$anken = preg_replace('/(.+)\[(入札開催日:)(.+)\]/','\1', $item->title);
		$limitdate = preg_replace('/(.+)\[(入札開催日:)(.+)\]/','\2<br />\3', $item->title);
		}
		else
		{
		$anken = preg_replace('/(.+)\[期限:(.+)\]/','\1', $item->title);
		$limitdate = preg_replace('/(.+)\[期限:(.+)\]/','\2', $item->title);
		}

		$detail = "<a href=\"".$linkurl."\" target=_blank>詳細</a>";
	print "      <li class=\"items\"><p>".$anken."</p><span class=\"spc\">"
		.$limitdate."</span><span class=\"spr\">".$detail."</span></li>\n";
//	.date("Y年m月d日"  ,strtotime($item->pubDate))."</span><span class=\"spr\">".$detail."</span></li>\n";
	}
//	print "      <div class=\"ac_close_out\"><a href=\"javascript:void(0);\" class=\"ac_close\">閉じる</a></div>";
	print "    </ul>\n";
}
//  1階層目のカテゴリを取得して表示
function getTopCategory()
{
	$req="http://rssfeed.deecorp.jp/dem/category_list.rss";
	$xml=httpRequest($req);
//	print "  <tr><th>カテゴリ</th><th>更新日</th></tr>\n";
	$cate_id=1;
	foreach($xml->channel->item as $item)
	{
		if ($cate_id==1) print "  <ul class=\"left_ul\">\n";
		//13行で折り返し
		if ($cate_id==14) print "  \n</ul>  <ul class=\"right_ul\">\n";

		$catestr=sprintf("cate_%03d",$cate_id);
		if (preg_match("/[0-9]+件/ui",$item->title))
		{
//			$title = "<a href=\"javascript:getLinks('".$catestr."','".$item->link."');\" class=\"accordion_head\">".$item->title."</a>";
			$title = "<a href=\"javascript:void(0);\" class=\"accordion_head\" onclick=\"getLinks('".$catestr."','".$item->link."');\">".$item->title."</a>";
		
	print "    <li>".$title."\n";
	print "      <div class=\"accordion_body\">\n";
	print "        <div id=\"".$catestr."\" ></div>\n";
	print "        <div class=\"ac_close_out\"><a href=\"javascript:void(0);\" class=\"ac_close\">閉じる</a></div>\n";
	print "      </div>\n";
	print "    </li>\n";
		}
		else
		{
		$title = "<span>".$item->title."</span>";
	print "    <li>".$title."\n";
	print "    </li>\n";
		}
		$cate_id++;
	}
	if ($cate_id==1) 
	print "<h3>サイトが混み合っているため最新案件情報を取得できません。しばらく時間をあけてアクセスしてください</h3>\n";
	else
	print "</ul>\n";
}
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
// エラーメッセージ表示
function errmsg($msg)
{
        print "\n<br><font color=red>ERROR: ".h($msg)."</font><br>";
}


?>
