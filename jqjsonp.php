<?php
ob_start();

require_once("n3/n3header.php");

function escapedbl($str){
	$str=	str_replace("\"", "'", $str);
	$str=	str_replace("&quot;", "'", $str);
	return $str;
}
function n($str){
	if (is_null($str)) return true;
	if ($str=="") return true;
	return false;
}

function p($str) {
	return pg_escape_string($str);
}
function h($str) //　改行をBRに変換
{
	$str=htmlspecialchars($str,ENT_QUOTES);
	$str = str_replace("\r\n","<br />",$str);
	$str = str_replace("\n","<br />",$str);
	return ($str);
}

function getIdfromToken(){
	global $db,$table_name,$key_name,$key_val,$col_name,$col_val,$token;

	$select1 = "select ".p($key_name)
	." from ".p($table_name)." where token='".p($token)."' ";
	$rows = pg_query($db, $select1);
	while ($row = pg_fetch_row($rows)) {
		$key_val = $row[0];
	}
	return $key_val;
}
function checkToken() {
	global $db,$token;

	if (is_null($token)) return false;
	if ($token=="") return false;

	$select1 = "select token,ip ,update_date"
	." from token where token='".p($token)."' ";

	$rows = pg_query($db, $select1);
//var_dump($select1);
	while ($row = pg_fetch_assoc($rows)) {
		return true;
	}
	print "No Token:".$token;
	return false;
}
function createToken($ip="")
{
	global $db,$response;

// delete old token
	$select1 = "delete from token "
	." where update_date < now() - interval '24 hours' ";
	$rows = pg_query($db, $select1);

	$now_time = time();// アクセス時刻
	$token = md5("Dee2".$now_time);
//var_dump($token);

	$select1 = "insert into token(token,ip,update_date) "
		." values ('".p($token)."','".p($ip)."',now())";
	$rows = pg_query($db, $select1);
	if (pg_affected_rows($rows)!=1)
	{
//	      throw new Exception("set Token ERROR");
		print(" set token error");
		$response["error"]="set token error";
	}
	else
	{
	// print"{ \"token\" : \"".$token."\" }\n";
		$response["token"]=$token;
	}
}


function updateInsert()
{
	global $db,$table_name,$key_name,$key_val,$col_name,$col_val,$token;

	if (!checkToken()) return;

	if (n($key_name)) $key_name=$table_name."_id";

	if (!n($key_val)){ //キー値が指定されているときだけ
		$select1 = "update ".p($table_name)." set ".p($col_name)." = '"
		.p($col_val)."', update_date=now() ,token=null where ".p($key_name)." = " .p($key_val)." ";
//var_dump($select1);
		$rows = pg_query($db,$select1);
		if ($numofrows=pg_affected_rows($rows)>0) return " \"UDPATE\" : \"".$numofrows."\" ";
	}
	else {
		$select1 = "insert into ".p($table_name)." (".p($col_name).", token) values('"
		.p($col_val)."', '".p($token)."')";
		$rows = pg_query($db,$select1);
//var_dump($select1);
		if ($numofrows=pg_affected_rows($rows)>0) return " \"INSERT\" : \"".getIdfromToken()."\" ";
	}
	return  " \"DML\" : \"ERROR\" ";
}

function uploadImage($img_name,$img_size,$img_type,$img_tmp){

	global $method,$response;
	$uploaddir = './upload/';
	$uploadfile = $uploaddir . basename($img_name);
//var_dump($uploadfile);
	if ($img_size > 10000000) {
		print "{ \"method\" : \"".$method."\",\n";
		print "  \"error\": \"size over\"  ";
		$response["error"]="size error";
		print "}\n";
		
		return;
	}
	if (move_uploaded_file($img_tmp, $uploadfile)) {
		print "{ \"method\" : \"".$method."\",\n";
		$response["filename"]=basename($img_name);
		print "  \"filename\": \"" . basename($img_name)."\" ,\n";
		$response["size"] = $img_size;
		print "  \"size\": \"" . $img_size."\" ,\n";
		$response["type"] = $img_type;
		print "  \"type\": \"" . $img_type."\" \n";
		print "}\n";
	} else {
		print "{ \"method\" : \"".$method."\",\n";
		$response["error"] = "cannot save";
		print "  \"error\": \"cannot save file\"  ";
		print "}\n";
	}
	return;
}
function selectAll() {
	global $response,$db,$method,$table_name,$key_name,$key_val,$col_name,$col_val,$orderby,$where,$maxcont,$maxcount;
	if (n($key_name)) $key_name=$table_name."_id";
	$wherestr="";

	if (!n($key_val)) $wherestr= " where ".p($key_name)." = '" .p($key_val)."' ";
	else $wherestr = " where 1=1 ";
	if (!n($where)) $wherestr.= "and ".$where;

	$orderstr="";
	if (!n($orderby)) $orderstr=" order by ".p($orderby);

	$select1 = "select * from ".p($table_name).$wherestr.$orderstr;
//var_dump($select1);

//var_dump( $select1);
	$rows = pg_query($db, $select1);

	$numofcols= pg_num_fields($rows); //columnの数
	$colname=array();
	for ($i=0;$i<$numofcols;$i++) $colname[$i]=pg_field_name($rows, $i); //column名
//var_dump($colname);

//	print"{ \"method\" : \"".$method."\",\n";
//	if ($retstr!="") print "  ".$retstr."  ,\n";
//	print"  \"".$table_name."\" :\n";
//	print"  \"table\" :\n";
//	print"  [\n";
	$i=0;
	while($row = pg_fetch_row($rows)) {
//var_dump($row);
//		if ($i>0) print ",\n";
//		print"     {\n";
		for ($j=0;$j<$numofcols;$j++) {
//			if ($j>0) print ",\n";
//			print "     \"".$colname[$j] ."\" : \"". $row[$j] ."\" ";
$response[$table_name][$i][$colname[$j]] = escapedbl($row[$j]);
		}
//		print "\n     }";

		$i++;
		if ($i>=$maxcount) break;
	}
//	print"\n  ]\n";
//	print"  ,\"count\" : \"".$i."\"\n";
$response["count"]=$i;
//	print"}\n";
	return $retstr;
}
//tableの前提: tokenという名称の列（文字列) が存在すること(insert直後のID取得用)
//      一意キーが存在し、列名が table名+"_id"であること	
// table名はしぼらないとあぶない

// mode
// insert: method=post  and  table_name/col_name/col_val/token
// update: method=post  and  table_name/col_name/col_val/key_name/key_val/token
// select: method=get/post and table_name (col_name/col_val/order_val)
// token:  method=get and no parameter
// img:  method=post and img_path

	$table_name=null;
	$key_name=null;
	$key_val=null;
	$col_name=null;
	$col_val=null;
	$method="TOKEN";
	$token=null;
	$retstr="";
	$callback="callbak";
	$where="";
	$orderby="";
	$maxcount=10;

	if (isset($_REQUEST['callback'])) { $callback = $_REQUEST['callback']; }
	if (isset($_REQUEST['table_name'])) { $table_name = $_REQUEST['table_name']; }
	if (isset($_REQUEST['key_name'])) { $key_name = $_REQUEST['key_name']; }
	if (isset($_REQUEST['key_val'])) { $key_val = $_REQUEST['key_val']; }
	if (isset($_REQUEST['col_name'])) { $col_name = $_REQUEST['col_name']; }
	if (isset($_REQUEST['col_val'])) { $col_val = $_REQUEST['col_val']; }
	if (isset($_REQUEST['token'])) { $token = $_REQUEST['token']; }

	if (isset($_POST['table_name'])) { $method="POST"; }
	if (isset($_GET['table_name'])) { $method="GET"; }

	if (isset($_REQUEST['where'])) { $where = stripslashes(urldecode($_REQUEST['where'])); }
	if (isset($_REQUEST['orderby'])) { $orderby = stripslashes(urldecode($_REQUEST['orderby'])); }
	if (isset($_REQUEST['maxcount'])) { $maxcount = $_REQUEST['maxcount']; }
//var_dump($where);
//var_dump($orderby);

	if (isset($_FILES["img_path"])) $method="IMG"; //img_pathがあったときは"IMG


	$response=array("method"=>$method, $table_name => array());

if (isset($_REQUEST['col_val'])) { $method="POST"; } //仮

	print $callback."(\n";  //jquery用にcallback関数名を返す

	if ($method=="POST") updateInsert();
	if ($method=="TOKEN") createToken();
//var_dump($method);
	if (($method=="GET")||($method=="POST")) selectAll();

	if ($method=="IMG"){
		$img_name = $_FILES["img_path"]["name"];
		$img_size = $_FILES["img_path"]["size"];
		$img_type = $_FILES["img_path"]["type"];
		$img_tmp = $_FILES["img_path"]["tmp_name"];
		uploadImage($img_name,$img_size,$img_type,$img_tmp);

	}
/*
print "<br />";
print "<br />";
print "<br />";
var_dump($response);
print "<br />";
print "<br />";
print "<br />";
*/
	print json_encode($response);

	print ");\n";   //jsonpの最後


	pg_close($db);
$content=ob_get_contents();
ob_end_clean();
echo $content;

?>
