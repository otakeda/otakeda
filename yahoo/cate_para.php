<?php 
/**
 *	 パラメータ定義
 **/


	$url="";
	$rank=null;

	// url: used by catelist2.php, for rss url.
        if (isset($_GET['url'])) { $url = $_GET['url']; }
        if (isset($_POST['url'])) { $url = $_POST['url']; }

?>
