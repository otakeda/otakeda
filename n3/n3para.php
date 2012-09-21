<?php 
/*	This file is written in utf8
 *	
 *	Created:  2012/01
 * 	Updated: 
 *  	Author :  Osamu Takeda
*/


	$player=null;    // 1 or 2
	$status =null;   // entry => answer => hit => end
        $hits =null;      //  1=>2=>....  
        $hit =null;      //  3numeric
	$game_key=null;
	$answer=null;   // 3 numeric
	$table_name=null;
	$key_name=null;
	$col_name=null;
	$col_val=null;
	$order_val=null;


        if (isset($_GET['player'])) { $player = $_GET['player']; }
        if (isset($_POST['player'])) { $player = $_POST['player']; }

        if (isset($_GET['status'])) { $status = $_GET['status']; }
        if (isset($_POST['status'])) { $status = $_POST['status']; }

        if (isset($_REQUEST['game_key'])) { $game_key = $_REQUEST['game_key']; }
        if (isset($_REQUEST['hits'])) { $hits = $_REQUEST['hits']; }
        if (isset($_REQUEST['hit'])) { $hit = $_REQUEST['hit']; }
        if (isset($_REQUEST['answer'])) { $answer = $_REQUEST['answer']; }

        if (isset($_REQUEST['table_name'])) { $table_name = $_REQUEST['table_name']; }
        if (isset($_REQUEST['key_name'])) { $key_name = $_REQUEST['key_name']; }
        if (isset($_REQUEST['col_name'])) { $col_name = $_REQUEST['col_name']; }
        if (isset($_REQUEST['col_val'])) { $col_val = $_REQUEST['col_val']; }
        if (isset($_REQUEST['order_val'])) { $order_val = $_REQUEST['order_val']; }
	
?>
