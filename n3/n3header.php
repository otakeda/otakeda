<?php
define ("SERVENV","stage");

        $db=null;
        if ($db = pg_connect("host=svxpgs11 dbname=qaservice user=qaservice password=qaservice")){
        }
        else { print " connect error "; exit;}

$stage=true;
if (SERVENV!="hon")
$consumer_key = "UIRjjejc0733zAX3aOuw"; //stage
else
$consumer_key = "J8IsoLp5Y5kShfxp5u5yA"; //hon

// Consumer secretの値
if (SERVENV!="hon")
$consumer_secret = "ZkvWkeW6zRH8yu2qXi396qWnUVWZ7syaQdsUVEo4"; //stage
else
$consumer_secret = "NaIZr0LAvg1fmROv2qT2bhPle5J0ErdeKOtlgJQGjAs"; //hon

if (SERVENV!="hon")
{
        $yahoo_id='hJwVm5uxg66HwDadLIx.keRD3CjKfKjHTAYoDr4ThzxY3w_PblAYj4crpbUwg3t4Vn8-'; //stage
	define ("YAHOO_ID","hJwVm5uxg66HwDadLIx.keRD3CjKfKjHTAYoDr4ThzxY3w_PblAYj4crpbUwg3t4Vn8-");
}
else
{
        $yahoo_id='oXIHz8uxg65do.238Rz2gF0lhxUmz5JHvRnUiSRozfqZXbpXxG6PfMS5AIE7nNZUYx8-'; //hon
	define ("YAHOO_ID","oXIHz8uxg65do.238Rz2gF0lhxUmz5JHvRnUiSRozfqZXbpXxG6PfMS5AIE7nNZUYx8-");
}

/*
var_dump(SERVENV);
var_dump($consumer_key);
var_dump($consumer_secret);
*/
        $admin_screen_name="q2dee";
        $admin_user_id = "159875224";  // book110 のuserid

	define ("ADMIN_SCREEN_NAME","q2dee");
	define ("ADMIN_USER_ID","159875224");
	define ("ADMIN_MEMBER_ID","12");
	define ("PRECORP","(例:ディーコープ株式会社)");
	define ("PREURL","(例: http://deecorp.jp/)");
        define ("PRETAG","(例:DeeCorp,見積,経営支援,リバースオークション)");
        define ("PREADDRESS","(例: 東京都港区)");
	define ("PRETEXT","(この会社のクチコミ情報：会社の雰囲気/どんな人がいるか/経営状態/その他)");
	define ("PRESEARCH","(会社名を入力してください)");

?>

