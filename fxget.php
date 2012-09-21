<!DOCTYPE html>
<html>
<body>
aaaaaaaaa\n
<?php
ob_start();

require_once("simplehtmldom_1_5/simple_html_dom.php");

require_once("corpheader.php");
require_once("corpfnc.php");

function getFromReuters($src,$dest){
        $request = "http://jp.reuters.com/investing/currencies/quote?srcAmt=1.0&srcCurr=".$src."&destCurr=".$dest;
        $html=str_get_html(httpRequest0($request));

	if (is_null($html)) print "No response<br>";
	foreach($html->find(".quoteLast") as $ql) {
	print "src=".$src.":dest=".$dest."=".$ql->plaintext."<br />";
	}
//	var_dump( $html->find("#contentBand"));
}


getFromReuters("USD","JPY");
getFromReuters("EUR","JPY");
getFromReuters("EUR","USD");
?>

</body>
</html>

<?php
 pg_close($db);

$content=ob_get_contents();
ob_end_clean();
echo $content;

?>
