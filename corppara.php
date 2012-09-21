<?php 
/*	This file is written in utf8
 *	
 *	Created:  2011/02
 * 	Updated: 
 *  	Author :  Osamu Takeda
*/


	$error_flg=false;
//        $reply_id=null;
	$is_reply=true;
	$twitext=null;
	$qa_flg='n';
	$delete_flg='0';
	$nowpage1=1;
	$nowpage2=1;
	$searchword="";// Only used by corp.php
	$corpid=null;// Only used by corp.php
	$url="";
	$address="";
	$corp_name=null;
	$corp_tag="";
	$user_type_id=0;
	$user_star=0;
	$agree_flag=false;
	$searchtext="";
	$videotag="";
	$repid=null;
	$sid=null;
	$term="days";


        if (isset($_POST['agree'])) { $agree_flag=true; }
//var_dump($agree_flag);

/*
	if (!($reply_id >0)) { $error_flg=true; $reply_id=null; }

        if (isset($_GET['qa_flg'])) { $qa_flg = $_GET['qa_flg']; }
        if (isset($_POST['qa_flg'])) { $qa_flg = $_POST['qa_flg']; }
	if (($qa_flg!='q')&&($qa_flg!='a')) $qa_flg='n';
*/

        if (isset($_GET['delete_flg'])) { $delete_flg = $_GET['delete_flg']; }
        if (isset($_POST['delete_flg'])) { $delete_flg = $_POST['delete_flg']; }

	//all
        if (isset($_GET['nowpage1'])) $nowpage1 = $_GET['nowpage1']; else $nowpage1=1;
	//my
        if (isset($_GET['nowpage2'])) $nowpage2 = $_GET['nowpage2']; else $nowpage2=1;

        if (isset($_GET['defbox'])) $defbox = $_GET['defbox']; 
        else
        {
	        if (isset($_POST['defbox'])) $defbox = $_POST['defbox']; else $defbox=1;
        }

	if (isset($_GET['twitext'])) $twitext = $_GET['twitext'];
	else if (isset($_POST['twitext'])) $twitext = $_POST['twitext'];

	if (isset($_GET['searchword'])) $searchword = $_GET['searchword'];
	else if (isset($_POST['searchword'])) $searchword = $_POST['searchword'];
	if (!is_null($searchword)) 
	{
		$searchword = trim($searchword);
	}
	if (isset($_GET['c'])) $corpid = $_GET['c'];
	else if (isset($_POST['c'])) $corpid = $_POST['c'];
	if (isset($_GET['r'])) $repid = $_GET['r'];
	else if (isset($_POST['r'])) $repid = $_POST['r'];
	if (isset($_GET['s'])) $sid = $_GET['s'];
	else if (isset($_POST['s'])) $sid = $_POST['s'];

	if (isset($_GET['mid'])) $memberid = $_GET['mid'];
	else if (isset($_POST['mid'])) $memberid = $_POST['mid'];

	if (isset($_POST['url'])) $url = $_POST['url'];
	if ($url==PREURL) $url="";
	if (isset($_POST['address'])) $address = $_POST['address'];
	if ($address==PREADDRESS) $address="-";
	if (isset($_POST['corp_name'])) $corp_name = $_POST['corp_name'];
	if ($corp_name==PRECORP) $corp_name="-";
	if (isset($_POST['corp_tag'])) $corp_tag = $_POST['corp_tag'];
	if ($corp_tag==PRETAG) $corp_tag="";
	if (isset($_POST['user_type_id'])) $user_type_id = $_POST['user_type_id']; else $user_type_id=0;
        if (isset($_POST['user_star'])) $user_star = $_POST['user_star']; else $user_star=0;

	if (isset($_GET['searchtext'])) $searchtext = $_GET['searchtext'];
	else if (isset($_POST['searchtext'])) $searchtext = $_POST['searchtext'];

	if (isset($_REQUEST['videotag'])) $videotag = $_REQUEST['videotag'];

	if (isset($_GET['term'])) $term = $_GET['term'];
	else if (isset($_POST['term'])) $term = $_POST['term'];

// 'や"に\が自動でつくこと防止
	if (get_magic_quotes_gpc()){ 
		$twitext = stripslashes($twitext); 
		$corp_name=stripslashes($corp_name);
		$url=stripslashes($url);
		$address=stripslashes($address);
		$corp_tags=stripslashes($corp_tags);
		$videotag = stripslashes($videotag); 

	}
// twitext以外はHTMLタグ利用禁止
		$corp_name = strip_tags($corp_name);
		$address = strip_tags($address);
		$url = strip_tags($url);
		$corp_tags = strip_tags($corp_tags);
		$searchtext = strip_tags($searchtext);
//var_dump($user_star);
//var_dump($user_type_id);
//201208脆弱性対策
		$corpid = strip_tags($corpid);
		$repid = strip_tags($repid);
		$sid = strip_tags($sid);
		$memberid = strip_tags($memberid);
		$searchword = strip_tags($searchword);
		$nowpage1 = strip_tags($nowpage1);
		$defbox = strip_tags($defbox);
		$user_type_id = strip_tags($user_type_id);
		$user_star = strip_tags($user_star);
		$term = strip_tags($term);
		$videotag = strip_tags($videotag);

?>
