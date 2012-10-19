<?php
ob_start();
//require_once("meishi_jsonhead.php");

require_once("meishi_header.php");


// method valiables
// UPDATE: method=post and member_id/client_id/img_path/lati/longi
// RELATION: method=post and member_id/client_id/partner_id  make Relation
// SELECT: method=get and member_id/client_id                ONLY myown info.
// SEARCH: method=get and member_id/client_id/lati/longi     GPS Search
// ADMIN: method=get and member_id/client_id/admin           Relation Search
// TOKEN: method=get and no parameter

        $placemarks = array();
	$json_array=array();
	$member_id=null;
	$client_id=null;
	$order_val=null;
	$mode=null;
	$token=null;
	$retstr="";
	$img_exists=false;
	$lati=null;
	$longi=null;
	$admin=null;
	$partner_id=null;

	$table_name="meishi";
	$key_name="meishi_id";
	$col_name="imgfile";

	$xml_title = null;
	$xml_link = null;
	$json_str = "";
	$xml_creator = null;
	$xml_updatedate = null;

	if (isset($_REQUEST['member_id'])) { $member_id = $_REQUEST['member_id']; }
	$key_val=$member_id;
	if (isset($_REQUEST['client_id'])) { $client_id = $_REQUEST['client_id']; }
	if (isset($_REQUEST['mode'])) { $mode = $_REQUEST['mode']; }
	if (isset($_REQUEST['order_val'])) { $order_val = $_REQUEST['order_val']; }
	if (isset($_REQUEST['token'])) { $token = $_REQUEST['token']; }
	if (isset($_REQUEST['lati'])) { $lati = $_REQUEST['lati']; }
	if (isset($_REQUEST['longi'])) { $longi = $_REQUEST['longi']; }
	if (isset($_REQUEST['admin'])) { $admin = $_REQUEST['admin']; }
	if (isset($_REQUEST['partner_id'])) { $partner_id = $_REQUEST['partner_id']; }
	
	$method="TOKEN";
	if (isset($_POST['client_id'])) { $method="UPDATE"; }
	if (isset($_GET['client_id'])) { $method="SELECT"; }
	if (isset($_POST['partner_id'])) { $method="RELATION"; }

	if (isset($_GET['lati'])) { $method="SEARCH"; }
	if (isset($_POST['lati'])) { $method="SEARCH"; }
	if (isset($_GET['admin'])) { $method="ADMIN"; }
if (isset($_GET['token'])) { $method="UPDATE"; }
if (isset($_GET['partner_id'])) { $method="RELATION"; }

	if (isset($_FILES["img_path"]) && !n($_FILES["img_path"]["name"])) $img_exists = true;

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
	global $db,$table_name,$key_name,$key_val,$token;

	$select1 = "select ".p($key_name)
	." from meishi where token='".p($token)."' ";
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
	return false;
}
function createToken($ip="")
{
	global $db,$json_str;

	$ret=0;
// delete old token
	$select1 = "delete from token "
	." where update_date < now() - interval '72 hours' ";
	$rows = pg_query($db, $select1);

	$now_time = time();// アクセス時刻
	$token = md5("Dee2".$now_time);
//var_dump($token);

	$select1 = "insert into token(token,ip,update_date) "
		." values ('".p($token)."','".p($ip)."',now())";
	$rows = pg_query($db, $select1);
	if (pg_affected_rows($rows)!=1)
	{
		$json_str.=" \"TOKEN\" : \"ERROR\", ";
		$ret=801;
		
	}
	else
	$json_str.=" \"token\" : \"".$token."\", \n";
	return $ret;
}


function updateInsert(&$member_id) {

	global $db,$table_name,$key_name,$col_name,$img_exists,$token,$img_path, $json_str,$lati,$longi;

//	if (!checkToken()) return 106;  //tokenパラメータが不正

	$ret=0;
	$update_cols="";
	$insert_cols="";
	if ($img_exists){
		if (preg_match('/(\.jpg|\.png|\.gif)/i', $_FILES["img_path"]["name"])) {
			$img_name = $_FILES["img_path"]["name"];
			$img_size = $_FILES["img_path"]["size"];
			$img_type = $_FILES["img_path"]["type"];
			$img_tmp = $_FILES["img_path"]["tmp_name"];
			$ret= uploadImage($img_name,$img_size,$img_type,$img_tmp);
		}else{
			$ret=100; //拡張子が画像じゃない
		}
		$col_val=$img_name;
		$update_cols=" imgfile = '".p($col_val)."', ";
		$insert_cols="'".p($img_name)."',";
	}
	else $insert_cols="'default.gif',";

	if ($ret > 0) return $ret;
	
	if ($lati>0 && $longi>0) {
//var_dump($lati+$longi);
		$update_cols=" lati=".p($lati).",longi=".p($longi).",search_date=now(), ";
	}

	if (!n($member_id)){ //キー値が指定されているときだけ
		$select1 = "update meishi set ".$update_cols
		." update_date=now() ,token=null where meishi_id = " .p($member_id)." ";
//var_dump($select1);
		$rows = pg_query($db,$select1);
		if ($numofrows=pg_affected_rows($rows)>0) $json_str.= " \"UPDATE\" : \"".$numofrows."\", ";
		else {
			$json_str.=" \"UPDATE\" : \"ERROR\", ";
			$ret=105;
		}
	}
	else {
		$select1 = "insert into meishi (imgfile, token) values("
		.$insert_cols." '".p($token)."')";
		$rows = pg_query($db,$select1);
//var_dump($select1);
		$member_id=getIdfromToken();
		if ($numofrows=pg_affected_rows($rows)>0) $json_str.= " \"INSERT\" : \"".$member_id."\", ";
		else {
			$json_str.=" \"INSERT\" : \"ERROR\", ";
			$ret=106;
		}
	}
	return  $ret;
}
function checkMember($member_id){
	global $db,$token;

	if (n($member_id)) return false;

	$select1 = "select meishi_id"
	." from meishi where meishi_id=".p($member_id)." ";

	$rows = pg_query($db, $select1);
	if (pg_num_rows($rows) == 1) return true;
	else false;
}
function createRelation($member_id,$partner_id){
	global $db, $json_str;

	if (!checkMember($member_id) ) return 207;
	if (!checkMember($partner_id) ) return 208;
	$ret=0;
	$select1 = "update meishi_relation set "
	." update_date=now() where owner_id = " .p($member_id)." and partner_id=".p($partner_id)." ";
//var_dump($select1);
	$rows = pg_query($db,$select1);
//var_dump(pg_affected_rows($rows));
	if ($numofrows=pg_affected_rows($rows)>0) $json_str.= " \"UPDATE\" : \"".$numofrows."\", ";
	else {
		$select1 = "insert into meishi_relation (owner_id, partner_id) values("
		.p($member_id)." , ".p($partner_id).")";
		$rows = pg_query($db,$select1);
//var_dump($select1);
		if ($numofrows=pg_affected_rows($rows)>0) $json_str.= " \"INSERT\" : \"".$member_id."\", ";
		else {
			$json_str.=" \"INSERT\" : \"ERROR\", ";
			$ret=209;
		}
	}
	return  $ret;
}

function makeDir(){
}
function uploadImage($img_name,$img_size,$img_type,$img_tmp){

	global $method,$json_str;
	$uploaddir = './upload/';
	$uploadfile = $uploaddir . basename($img_name);
//var_dump($uploadfile);
	if ($img_size <= 10) {
		return 101;   // too small
	}
	if ($img_size > 10000000) {
		$json_str.= "\"imgfile\":{ ";
		$json_str.= "  \"size error\": \"".$img_size."\"  ";
		$json_str.= "},\n";
		return 102; // over 10MB
	}
	if (n(basename($img_name))) return 103; //filename is invalid

	if (move_uploaded_file($img_tmp, $uploadfile)) {
		$json_str.= "\"imgfile\":{ ";
		$json_str.= "  \"filename\": \"" . basename($img_name)."\" ,";
		$json_str.= "  \"size\": \"" . $img_size."\" ,";
		$json_str.= "  \"type\": \"" . $img_type."\" ";
		$json_str.= "},\n";
	} else {
		$json_str.= "\"imgfile\":{ ";
		$json_str.= "  \"error\": \"cannot save file\"  ";
		$json_str.= "},\n";
		return 104;  //failure of saving on server
	}
	return 0;
}
function printErrors($errno) {
	global $json_str;
	print " \"ERROR\":" .$errno."\n";
	return;
}
function selectMe($member_id) {
	global $db,$method,$table_name,$key_name,$order_val,$json_str;
	if (n($key_name)) $key_name=$table_name."_id";

	$wherestr= " where meishi_id = '" .p($member_id)."' ";
	$select1 = "select * from meishi ".$wherestr;
	$rows = pg_query($db, $select1);
//var_dump($select1);
	$json_str.="\"SELECT\":\"".pg_num_rows($rows)."\",\n";
	while($row = pg_fetch_assoc($rows)) {
	$json_str.="\"mycard\":";
	$json_str.= "{ \"meishi_id\":\"".$row['meishi_id']."\",\n";
	$json_str.= " \"imgfile\":\"upload/".$row['imgfile']."\",\n";
	$json_str.= " \"lati\":\"".$row['lati']."\",\n";
	$json_str.= " \"longi\":\"".$row['longi']."\",\n";
	$json_str.= " \"update_date\":\"".$row['update_date']."\"},\n";
	return true;	
	}
	return false;
}
function selectAdmin($member_id) {
	global $db,$method,$table_name,$lati,$longi,$order_val,$json_str,$json_array,$placemarks;
	if (n($key_name)) $key_name=$table_name."_id";

	$wherestr= " where 1=1";
	$select1 = "select m1.*,r.update_date as r_update_date from meishi m0 join meishi_relation r on r.owner_id=m0.meishi_id "
	 	." join meishi m1 on m1.meishi_id=r.partner_id "
		." where m0.meishi_id=".p($member_id) 
		." order by r.update_date desc";
	$rows = pg_query($db, $select1);
//var_dump($select1);
	$json_str.="\"SELECT\":\"".pg_num_rows($rows)."\",\n";
	$json_str.="\"cards\": [ ";

	$i=0;
	while($row = pg_fetch_assoc($rows)) {
		if ($i!=0) $json_str.=",\n";
		$json_str.= "{ \"meishi_id\":\"".$row['meishi_id']."\",\n";
		$json_str.= " \"imgfile\":\"upload/".$row['imgfile']."\",\n";
		$json_str.= " \"lati\":\"".$row['lati']."\",\n";
		$json_str.= " \"longi\":\"".$row['longi']."\",\n";
		$json_str.= " \"search_date\":\"".$row['search_date']."\",\n";
		$json_str.= " \"update_date\":\"".$row['update_date']."\",\n";
		$json_str.= " \"r_update_date\":\"".$row['r_update_date']."\"}\n";
		$i++;
	}
	$json_str.=" ]\n";
	$json_str.= ",\n";
	return false;
}
function selectSearch($member_id) {
	global $db,$method,$table_name,$lati,$longi,$order_val,$json_str,$json_array,$placemarks;
	if (n($key_name)) $key_name=$table_name."_id";

	$wherestr= " where 1=1";
	if ($lati>0&&$longi>0) $wherestr.=" and lati >0 and longi > 0 and meishi_id!=".$member_id
			. " and search_date > now() - interval '240 hours' ";
	$select1 = "select * from meishi ".$wherestr. " order by search_date desc";
	$rows = pg_query($db, $select1);
//var_dump($select1);
	$json_str.="\"SELECT\":\"".pg_num_rows($rows)."\",\n";
	$json_str.="\"cards\": [ ";

	$i=0;
	while($row = pg_fetch_assoc($rows)) {
		if ($i!=0) $json_str.=",\n";
		$json_str.= "{ \"meishi_id\":\"".$row['meishi_id']."\",\n";
		$json_str.= " \"imgfile\":\"upload/".$row['imgfile']."\",\n";
		$json_str.= " \"lati\":\"".$row['lati']."\",\n";
		$json_str.= " \"longi\":\"".$row['longi']."\",\n";
		$json_str.= " \"search_date\":\"".$row['search_date']."\",\n";
		$json_str.= " \"update_date\":\"".$row['update_date']."\"}\n";

        // set Placemark Properties
/*
		if ($row['lati']>0&&$row['longi']>0) {
       		array_push($placemarks,
                //マーカーに関する情報・ここから
                array(
                        'name' => $row['meishi_id'],
                        'description' => 'aa',
//                        'description' => '<img src="upload/'.$row['imgfile'].'" width=30/>',
                        'url' => '',
                        'lookat' => array(
//                                'latitude' => $row['lati'],
 //                               'longitude' => $row['longi']
                                'latitude' => $lati,
                                'longitude' => $longi
                        ),
                        'icon' => 'orangeDot'
                )
		);
		}
*/
		$i++;
	}
	$json_str.=" ]\n";
	$json_str.= ",\n";
/*
                               header('Content-type: application/json; charset=utf-8');
                                header('Last-Modified: '.gmdate( 'D, d M Y H:i:s' ).' GMT');
                                header('pragma: no-cache');
                                header("Cache-Control: no-cache, must-revalidate");
                                header("Expires: Tue, 31 Mar 1981 05:00:00 GMT");

*/
	return false;
}
	$ret=0;
	if ($method!="0SEARCH") print "{";  
	print " \"METHOD\":\"".$method."\" ,";
	switch($method) {
		case "TOKEN" :
			$ret=createToken();
			print $json_str;
			break;
		case "UPDATE" :
			$ret = updateInsert($member_id);
		case "SELECT" :
			if (n($member_id)) $ret=201;
			else if (!selectMe($member_id)) $ret=202; //no data found
			print $json_str;
			break;
		case "SEARCH" :
			$ret = updateInsert($member_id);
			selectSearch($member_id);
			print $json_str;
			break;
		case "ADMIN" :
			selectAdmin($member_id);
			print $json_str;
			break;
		case "RELATION" :
			$ret=createRelation($member_id, $partner_id);
			print $json_str;
			break;
	}
	if ($method!="0SEARCH") printErrors($ret);  
	if ($method!="-SEARCH") print "}";  

	pg_close($db);
//require_once("meishi_jsonfoot.php");
$content=ob_get_contents();
ob_end_clean();
echo $content;

?>
