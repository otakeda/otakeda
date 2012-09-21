<?php

require_once("twitteroauth.php");
//require_once "HTTP/Request.php";

function dtoken($db)
{
	$now_time = time();// アクセス時刻
	$now_date = gmdate("Ymd", $now_time); // アクセス日
	$dtoken = md5("Dee0".$now_time);
//	setcookie("dee_token", $dtoken, $now_time +  3600);
	$ip_address = $_SERVER["REMOTE_ADDR"];
	setToken($db,$dtoken,$ip_address);
//	print "<h2>Loginしてください</h2>\n";
	print "<p>\n";
	print "本サービスをより便利にお使いいただくには、Twitter（ツイッター）のIDとログインが必要です。";
	print "</p>\n";
	print "<p>\n";
       	print "<a href=\"?dtoken=".$dtoken."\">";
	print "<img id=\"twitterBtn\" src=\"images/btn_login.gif\" width=\"191\" height=\"38\" alt=\"Twitterにログイン\" /></a>\n";
	print "</p>\n";
	print "<p id=\"signup\">Twitter IDをお持ちでない方は";
	print "<a href=\"https://twitter.com/signup\" target=\"_blank\">こちら</a></p>\n";
	return $dtoken;
}

function getUserCounts($db)
{
	$ret=0;
        $select1 = "select count(*) as cnt" ." from members ";

        $rows = pg_query($db, $select1);
//var_dump($select1);
        while ($row = pg_fetch_assoc($rows))
        {
                $ret=$row['cnt'];
	}
	return $ret;
}
function setToken($db,$token,$ip="")
{
        $select1 = "delete from token "
        ." where update_date < now() - interval '72 hours' ";
        $rows = pg_query($db, $select1);

	$select1 = "insert into token(token,ip,update_date) "
		." values ('".$token."','".$ip."',now())";
	$rows = pg_query($db, $select1);
	if (pg_affected_rows($rows)!=1)
	{
//		throw new Exception("set Token ERROR");
		errmsg(" set token error");
	}
}
function checkToken($db,$token)
{
        $select1 = "select token,ip ,update_date"
        ." from token where token='".$token."' and update_date > now() - interval '12 hours' ";

        $rows = pg_query($db, $select1);
        $member_id = 0;
//var_dump($select1);
        while ($row = pg_fetch_assoc($rows))
        {
                return true;
        }
	return false;
}


function getMemberId($db, $user_id)
{
        $select1 = "select member_id,imgfile "
        ." from members where user_id='".$user_id."' ";

        $rows = pg_query($db, $select1);
        $member_id = 0;
//var_dump($select1);
        while ($row = pg_fetch_assoc($rows))
        {
                $member_id=$row['member_id'];
        }
	return $member_id;
}
function getMembers(&$img, $db,$access_token,$access_token_secret, $user_id, &$agree_flag)
{
        $select1 = "select member_id,imgfile ,agree_flag"
        ." from members where user_id='".$user_id."' "
        ." and access_token='".$access_token."'"
        ." and access_token_secret='".$access_token_secret."'";

        $rows = pg_query($db, $select1);
        $member_id = 0;
//var_dump($select1);
        while ($row = pg_fetch_assoc($rows))
        {
                $member_id=$row['member_id'];
                $img=$row['imgfile'];
		if($row['agree_flag']=='1') $agree_flag=true; else $agree_flag=false;
        }
        return $member_id;
}

function getUserValuation($userxml , $followerspts=null)
{
	$pts=0;
	$last_status=strtotime($userxml->status->created_at);
	$today = time();
//var_dump($today-$last_status);
	// 1: 1ヶ月以内に Tweetがある 
	if ($today-$last_status < 3600*24*30) $pts+=1;

	// 1: User作成から1年以上
	$created_at=strtotime($userxml->created_at);
	if ($today-$created_at > 3600*24*365) $pts+=1;

	// 1: Tweetが100以上
	if ($userxml->statuses_count  > 100) $pts+=1;

	// 1: フォロー数が5以上
	if ($userxml->friends_count  > 5) $pts+=1;

	// 4: フォロワー数が100以上
	if ($userxml->followers_count  > 0) $pts+=1;
	if ($userxml->followers_count  > 10) $pts+=1;
	if ($userxml->followers_count  > 100) $pts+=1;
	if ($userxml->followers_count  > 1000) $pts+=1;

	// 2: Listed数が10以上
	if ($userxml->listed_count > 0) $pts+=1;
	if ($userxml->listed_count > 10) $pts+=1;

//print "<br />".$userxml->screen_name.":".$pts.":".$followerspts;
	if (is_null($followerspts)) return $pts;
	else return ($pts + $followerspts)/2;
}
function setUserList($db,$userxml)
{
        $select1 = "update userlist set following=".$userxml->friends_count
	.", followers=".$userxml->followers_count
	.", statuses=".$userxml->statuses_count
	.", listed_count=".$userxml->listed_count
	.", last_status='".$userxml->status->created_at."'"
	.", created_at='".$userxml->created_at
	."' ,update_date=now()"
	." where user_id=".$userxml->id." ";
        $rows = pg_query($db, $select1);
	$is_success=false;
	if (pg_affected_rows($rows) < 1)
	{
		$select1 = "insert into userlist(user_id,screen_name,following,followers,"
		."statuses,listed_count,last_status,created_at,update_date)"
		." values (".$userxml->id
		.",'".$userxml->screen_name
		."',".$userxml->friends_count
		."," .$userxml->followers_count
		.",".$userxml->statuses_count
		.",".$userxml->listed_count 
		.",'".$userxml->status->created_at
		."','".$userxml->created_at."', now())";
		$rows = pg_query($db, $select1);
		if (pg_affected_rows($rows)==1)
		{
			printf("<br />add userlist [<font color=red>%s</font>]",$userxml->screen_name);
			$is_success=true;
		}
	       	else  errmsg("<br />userlist INSERT ERROR");
//:1var_dump($rows);
	}
	else
	{
		if (pg_affected_rows($rows) ==1)
		{
//printf("<br />update userlist [<font color=red>%s</font>]",$userxml->screen_name);
			$is_success=true;
		}
	       	else  errmsg("<br />userlist UPDATE ERROR");
	}
//var_dump($select1);
	return $is_success;
	
}
function getFollowers($to,$user_id)
{
	$xml = httpRequestXml("http://api.twitter.com/1/statuses/followers.xml?user_id=".$user_id);
//	$req = $to->OAuthRequest("http://api.twitter.com/1/statuses/followers.xml","GET",array("user_id"=>$user_id));
//	$xml = simplexml_load_string($req);
//var_dump($xml);
//	var_dump($xml->user);
//	print "<br />";
//	print $xml->user[0]->id;
//	print "<br />";
//	print $xml->user[1]->id;
//var_dump($xml);
	
	return $xml;
}
/*
	getTwitterUser: show twitter user info. by users/show interface 
	return : user info. (xml format )
	screen_name : 
*/
function getTwitterUser($screen_name)
{
	$req = "http://api.twitter.com/1/users/show.xml?screen_name=".$screen_name;
//	$req = "https://api.twitter.com/1/users/show.xml?screen_name=".$screen_name;
//	$xml=simplexml_load_file($req);
	$xml=httpRequest($req);
//var_dump($xml);
/*
        $name = $xml->name;  // ユーザーの名前（HNなど）
        $img = $xml->profile_image_url;  // image
        $utc_offset = $xml->utc_offset;   // 324000=9hours
	$screen_name = $xml->$screen_name;
        $following = $xml->friends_count;  
        $followers = $xml->followers_count;  // フォローされている数
        $statuses = $xml->statuses_count;    //ついーと数
        $user_created_at =$xml->created_at;  //作成日
        $listed_count =$xml->listed_count;  //リストに入れられている数
*/
	return $xml;

}
/*
	newLoginCheck:  called from twiAuth, creating user records and logging 
	$db:  DB handler
	$screen_name: Twitter user name 
*/
function newLoginCheck($db,$screen_name,$user_id,&$imgfile=null,$access_token,$access_token_secret, &$agree_flag=false, $username=null, $pts=null)
{
	$agree_str="";
	if ($agree_flag) $agree_str=" ,agree_flag='1' "; 
	if (!is_null($imgfile)) $agree_str.= ",imgfile='".pg_escape_string($imgfile)."' ";
	if (!is_null($username)) $agree_str.=",name='" .pg_escape_string($username)."' ";
	if (!is_null($pts)) $agree_str.=",pts=" .pg_escape_string($pts)." ";

        $select1 = "update members set update_date=now(),screen_name='".pg_escape_string($screen_name)."'"
        .",access_token='".$access_token."',access_token_secret='".$access_token_secret."'"
	.$agree_str
	." where user_id='".$user_id."' ";
        $rows = pg_query($db, $select1);
	$num_rows = 0;
	$member_id = 0;
	if (pg_affected_rows($rows) < 1)
	{
		$select1 = "insert into members(screen_name,create_date,imgfile,access_token,access_token_secret,user_id) "
		."values ('". pg_escape_string($screen_name)."' ,now(), '".pg_escape_string($imgfile)."','"
		.$access_token."','".$access_token_secret."','".$user_id."')";
		$rows = pg_query($db, $select1);
//var_dump($rows);
		if (pg_affected_rows($rows)==1)
			printf("<p>新規登録ありがとうございます :%s </p>",$screen_name);
	       	else  errmsg("New Member update ERROR<br />");
        }
        else
        {
                if (pg_affected_rows($rows)==1)
                        printf("<p>ご利用ありがとうございます  </p>",$screen_name);
                else  errmsg("UPDATE ERROR <br />");
        }

	$select1 = "select member_id ,agree_flag, imgfile from members where user_id='".$user_id."' ";
	$rows = pg_query($db, $select1);
	$member_id = 0;
	while ($row = pg_fetch_assoc($rows))
	{
		$member_id=$row['member_id'];
		if($row['agree_flag']=='1') $agree_flag=true; else $agree_flag=false;
		$imgfile=$row['imgfile'];
//var_dump($agree_flag);
	}
	return $member_id;
}


//ログイン状態であればmember_idを返す
function twiAuthCheck($db)
{
	session_start();
	$userimg=null;
	$user_id=null;
	$agree_flg=null;
			if (isset($_SESSION['oauth_access_token']))
				$access_token = $_SESSION['oauth_access_token']; else $access_token=null;
//var_dump($access_token);
			if (isset($_SESSION['oauth_access_token_secret']))
				$access_token_secret = $_SESSION['oauth_access_token_secret'];
			else $access_token_secret=null;

			$user_id= $_SESSION['user_id'];
//var_dump($user_id);
			$screen_name=$_SESSION['screen_name'];
			$member_id=getMembers($userimg,$db,$access_token,$access_token_secret,$user_id,$agree_flag);
	return $member_id;
}
/*
	twiAuth:   called from main, for Authentication and showing user information.
	$db:  DB handler
	$to:  Twitter Oauth handler
	$user_id:  Twitter user id
	$screen_name: Twitter user name 
	$consumer_key: Twitter app consumer key
	$consumer_secret: Twitter app secret key
*/
function twiAuth($db, &$to,&$user_id, &$screen_name,$consumer_key,$consumer_secret,$create_to,&$userimg=null,&$agree_flag=false)
{
	session_start();
	if (isset($_REQUEST['logoff']))
	{
	        if($_REQUEST['logoff'] === 'clear'){  // session の初期化
		session_destroy();
		session_start();
		$_SESSION=null;
		}
	}
//var_dump($consumer_secret);
//var_dump($_SESSION);

        if (isset($_SESSION['oauth_state'])) $state = $_SESSION['oauth_state'];
                else $state=null;

        if (isset($_SESSION['oauth_request_token']))
                $request_token = $_SESSION['oauth_request_token'];
        else
                $request_token = null;

        if (isset($_SESSION['oauth_request_token_secret']))
                $request_secret = $_SESSION['oauth_request_token_secret'];
        else
                $request_secret=null;
        if (isset($_REQUEST['oauth_token'])) $oauth_token = $_REQUEST['oauth_token'];
                else $oauth_token=null;

        if (isset($_REQUEST['section'])) $section = $_REQUEST['section']; else $section=null;

//認証からもどってきた直後
        if($oauth_token && $state === 'start')
                { $_SESSION['oauth_state'] = $state = 'returned'; 
		//print "-------\n"; 
		}
//var_dump($state);
        $member_id=0;
//        $userimg=null;
	$error_flg=false;

	if($state == 'returned'){
//		setcookie("dee_token", "", time() -3600);
//                print "<h2>Twitter User    <a href=?logoff=clear>Logout</a></h2>\n";
 
		if(!isset($_SESSION['oauth_access_token']) && !isset($_SESSION['oauth_access_token_secret'])){
//認証からもどってきた直後
try{
			$to = new TwitterOAuth($consumer_key,$consumer_secret,$request_token,$request_secret);
			$tok = $to->getAccessToken();
	//ログイン直後でエラーがでるのはここ twitter...auth.php 118行目
			$_SESSION['oauth_access_token'] = $access_token = $tok['oauth_token'];
			$_SESSION['oauth_access_token_secret'] = $access_token_secret = $tok['oauth_token_secret'];
			$_SESSION['user_id'] =  $tok['user_id'];
			if (!($tok['user_id']>0)) throw new Exception('USER_ID ERROR');
			$user_id=$tok['user_id'];
			$_SESSION['screen_name'] =  $tok['screen_name'];
			$screen_name=$tok['screen_name'];
//			if (!($user_id >0)) $error_flg=true;

                        $xml = getTwitterUser($screen_name);
			$pts = getUserValuation($xml);
//print "-----<br />\n";
//var_dump($pts);
                        $userimg= $xml->profile_image_url;
                        $member_id=newLoginCheck($db,$screen_name,$user_id,$userimg,$access_token,$access_token_secret,$agree_flag,$xml->name,$pts);
                        $create_to=true;
}catch(Exception $e){ throw new Exception('Twitter API (AUTH) ERROR');} 

//print "<br>tok0"; var_dump($tok);
		}
		else {
//２回目以降
			if (isset($_SESSION['oauth_access_token']))
				$access_token = $_SESSION['oauth_access_token']; else $access_token=null;
			if (isset($_SESSION['oauth_access_token_secret']))
				$access_token_secret = $_SESSION['oauth_access_token_secret'];
			else $access_token_secret=null;

			$user_id= $_SESSION['user_id'];
			$screen_name=$_SESSION['screen_name'];
			if ($agree_flag)   //同意するの直後
                        $member_id=newLoginCheck($db,$screen_name,$user_id,$userimg,$access_token,$access_token_secret,$agree_flag);
			else
			$member_id=getMembers($userimg,$db,$access_token,$access_token_secret,$user_id,$agree_flag);
//			if ($user_id==$admin_user_id) $create_to=true;

		}
		if ($create_to)
		$to = new TwitterOAuth($consumer_key,$consumer_secret,$access_token,$access_token_secret);
                else
                        print " ";
//print  "<br>tok1"; var_dump($to);
		print "<p>\n";
                print"<a href=\"http://twitter.com/" .$screen_name."\" target=\"_blank\">"
              		."<img src=\"".$userimg."\" style=\"float:left;margin-right:10px;\" width=\"42\" height=\"42\" border=\"0\" alt=\"".$screen_name."\"/></a></p>";
                print "<p>Twitter ID:".$screen_name." でログインしています</p>\n";
//                print "<br /><font color=white>".$member_id."/".$user_id."</font></p>\n";

//		print "</p>\n";
		print "<p id=\"signup\"><a href=\"?logoff=clear\">ログアウトする</a></p>\n";

	
//フォロワーリスト取得
/*
		$fxml=getFollowers($to,$user_id);
		$maxpts=0; $pts=0;
		foreach($fxml->user as $userxml)
		{
			setUserList($db,$userxml);
			$pts = getUserValuation($userxml);
			if ($maxpts< $pts) $maxpts=$pts;
		}
                $xml = getTwitterUser($screen_name);
		setUserList($db,$xml);
		$pts=getUserValuation($xml,$maxpts);
*/
        }
        else{
		if (!isset($_REQUEST['dtoken']))
		{
			$dtoken = dtoken($db);
		}
		else
		{
			$dtoken=$_REQUEST['dtoken'];
//ユーザなしで認証 => request tokenだけもってきておく
//var_dump($_SESSION);
//			print "<h2>Loginしてください</h2>\n";

			if (checkToken($db,$dtoken))
			{
		
       			$to = new TwitterOAuth($consumer_key,$consumer_secret);
				$tok=$to->getRequestToken();

				$token = $tok['oauth_token'];
				$_SESSION['oauth_request_token']=$token;

				$request_secret = $tok['oauth_token_secret'];
				$_SESSION['oauth_request_token_secret']=$request_secret;
				$_SESSION['oauth_state']='start';
				print "<p>\n";
				print "<a href=\"".$to->getAuthorizeURL($token)."\">Twitter.comへ転送しています...</a>\n";


				print "</p>\n";
				print "<p id=\"signup\">Twitter IDをお持ちでない方は";
				print "<a href=\"https://twitter.com/signup\" target=\"_blank\">こちら</a></p>\n";

				print "<script type=\"text/javascript\">\n";
				print "<!--\n";
				print "setTimeout('link()', 1000);\n";
				print " function link(){ location.href='".$to->getAuthorizeURL($token)."'; }\n";
				print "-->\n";
				print "</script>\n";
			}
			else
			{
			$dtoken=dtoken($db);

			}

		}

//var_dump($tok);
//var_dump($_SESSION);
        }
	print "<p>\n";
	printf("ただいまの登録ユーザ数:%d 人<br />",getUserCounts($db)+8100);
	print "</p>\n";

	return $member_id;
}
?>

