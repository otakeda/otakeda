<?php
ob_start();

require_once("n3header.php");
require_once("n3para.php");

function makeHit($db,$game_id ,$playerno,$hits,$hit,&$eats,&$bites)
{
	$eats=0; $bites=0;
	if ($playerno==1) $fellowno=2;
	if ($playerno==2) $fellowno=1;
        $select1 = "select answer".$fellowno." as answer from number3 where game_id=" .$game_id." ";
//var_dump($select1);
        $rows = pg_query($db,$select1);
        while($row = pg_fetch_assoc($rows))
	{
		$answer = $row['answer'];
	}
	$hitc[0]=substr($hit,0,1);
	$hitc[1]=substr($hit,1,1);
	$hitc[2]=substr($hit,2,1);
	$ansc[0]=substr($answer,0,1);
	$ansc[1]=substr($answer,1,1);
	$ansc[2]=substr($answer,2,1);
	for ($i=0; $i<3;$i++)
	{
//var_dump('ans:'.$i.':'.$ansc[$i]);
//var_dump('hit:'.$i.':'.$hitc[$i]);
		for ($j=0; $j<3;$j++)
		{
			if ($ansc[$i]==$hitc[$j]) 
			{	
				if ($i==$j) $eats++;
				else $bites++;
			}
		}
	}
        $select1 = "insert into number3_hit(hits,hit,playerno,game_id,eats,bites) values("
		.$hits.",'".$hit."', ".$playerno.", ". $game_id.",".$eats.",".$bites.")";
        $rows = pg_query($db,$select1);
//var_dump("makeHit:".$select1);
        if (pg_affected_rows($rows)==1) print" ";
        else  print "CANNOT CREATE hit record.";
}

function chkAnswer($db,$game_key,$player,&$game_id)
{
        $select1 = "select game_id,player1,player2 from number3 where game_key='" .$game_key."'";
        $rows = pg_query($db, $select1);
	$retval=0;
        while($row = pg_fetch_assoc($rows))
	{
		if ($row['player1']==$player ) $retval=1;
		if ($row['player2']==$player ) $retval=2;
		$game_id=$row['game_id'];
	}
	return $retval;
}
function makeAnswer($db,$game_id, $playerno,$answer)
{
        $select1 = "update number3 set answer".$playerno. "='".$answer."' where game_id="
		.$game_id." ";
        $rows = pg_query($db,$select1);
//var_dump("makeAnswer:".$select1);
        if (pg_affected_rows($rows)==1) print" ";
        else  print"   ";
	return $game_key;
}
function attend2nd($db,$game_key, $player2)
{
        $select1 = "update number3 set player2='".$player2."' , status='answer' where game_key='"
		.$game_key."' and status='entry' and player1!='".$player2."'";
//var_dump('attend2nd'.$select1);
        $rows = pg_query($db,$select1);
        if (pg_affected_rows($rows)==1) print" ";
        else  print"   ";
	return $game_key;
}
function createGame($db,$player1)
{
        $now_time = time();// アクセス時刻
        $now_date = gmdate("Ymd", $now_time); // アクセス日
        $game_key = md5("n3".$now_time);

        $select1 = "insert into number3(game_key,player1) values('".$game_key."', '".$player1."')";

        $rows = pg_query($db,$select1);
//var_dump($rows);
//        if (pg_affected_rows($rows)==1) print"[craete game]";
        if (pg_affected_rows($rows)==1) print" ";
        else  print"DB Access ERROR";
	return $game_key;
}
function getPlayerNo($db,$game_key,$player,&$game_id)
{
        $select1 = "select game_id,player1,player2 from number3 where game_key='" .$game_key."'";
        $rows = pg_query($db, $select1);
	$retval=0;
        while($row = pg_fetch_assoc($rows))
	{
		if ($row['player1']==$player ) $retval=1;
		if ($row['player2']==$player ) $retval=2;
		$game_id=$row['game_id'];
	}
	return $retval;
}

function getJsonRes($db,$game_id)
{

	if ($game_id > 0)
	{   // game_idをキーにして最新情報の取得
        $select1 = "select n.game_key,n.player1 ,n.player2,n.status,n.first,n.answer1,n.answer2"
		." ,h1.playerno,h1.hits, h1.hit as hit, h1.eats as eats, h1.bites as bites"
                ." from  number3 n "
		." left join number3_hit h1 on h1.game_id=n.game_id "
		."  where n.game_id=" .$game_id." "
                ." order by h1.hits,h1.playerno";
	}
	else
	{   //一番最近つくられて相手がまだいないものを１つ。ただし1時間以内
        $select1 = "select n.game_key,n.player1 ,n.player2,n.status,n.first,n.answer1,n.answer2"
		." ,0 as playerno,0 as hits, '0' as hit, 0 as eats, 0 as bites"
                ." from  number3 n "
		."  where status='entry' and entry_date > now() - interval '1 hour'"
                ." order by game_id desc limit 1";
	}
	

        $rows = pg_query($db, $select1);
//var_dump($select1);
        $j=0;   //データ行数
        $lastcorp=0;
        $scrtext = "";

	print "{ \n";
        while($row = pg_fetch_assoc($rows))
        {
		if ($j==0)
		{
		print "       \"game_key\": \"".$row['game_key']."\", ";
		print "       \"player1\": \"".$row['player1']."\", ";
		print "       \"player2\": \"".$row['player2']."\", ";
		print "       \"status\": \"".$row['status']."\", ";
		print "       \"answer1\": \"".$row['answer1']."\",";
		print "       \"answer2\": \"".$row['answer2']."\" ";
		print "  ,\"hit\":[ \n";
		}
		else	
		print "  ,\n";

		print "  { \n";
		print "         \"playerno\": \"".$row['playerno']."\", ";
		print "         \"hits\": \"".$row['hits']."\", ";
		print "         \"hit\": \"".$row['hit']."\", ";
		print "         \"eats\": \"".$row['eats']."\", ";
		print "         \"bites\": \"".$row['bites']."\" ";
		print "  } \n";
		
		$j++;
        }
        print "\n";
        if ($j!=0) print "  ] \n";
	print "} \n";
	if ($j > 0) return true; else return false;
}


	$breakc=true;
	$rec_count=0;
	// 一人目の参加   player
	if ((is_null($game_key))&&(!is_null($player))) { $game_key=createGame($db,$player); $breakc=false;}


	// 二人目の参加  game_key+player
	if (($breakc)&&(!is_null($game_key))&&(!is_null($player))&&(is_null($answer))&&(is_null($hits))) { attend2nd($db,$game_key,$player); $breakc=false;}


	$game_id=null;
	if (!is_null($game_key))
	$playerno=getPlayerNo($db,$game_key,$player, $game_id);

//if ($playerno==0) var_dump('No Player:'.$game_key.':'.$player);
//else print "Player=".$playerno;
	// 答えの設定  game_key+player+answer
	if (($breakc)&&(!is_null($game_id))&&($playerno>0)&&(!is_null($answer))) 
		{ makeAnswer($db,$game_id,$playerno,$answer); $breakc=false;} 

	$eats=0; $bites=0;
	// あてる    game_key+player+hits+hit
//var_dump('hit:='.$hit.':'.$game_id.':'.$hits.':'.$breakc);
	if (($breakc)&&(!is_null($game_id))&&($playerno>0)&&(!is_null($hits))&&(!is_null($hit))) 
		{ makeHit($db,$game_id,$playerno,$hits, $hit, $eats,$bites); $breakc=false;} 

/*
	print "GameKey:";
//var_dump($game_key);
*/
	if ($game_id > 0)
	$rec_count=getJsonRes($db,$game_id);
	else
	$rec_count=getJsonRes($db,$game_id);


//	var_dump( $searchword);

        pg_close($db);
$content=ob_get_contents();
ob_end_clean();
echo $content;

?>
