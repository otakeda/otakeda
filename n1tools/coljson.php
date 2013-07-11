 <?php
require_once 'coldef.php';


define ('KINSHI_CHK', '[\n\r\']');
define('IZON_CHK','([①②③④⑤⑥⑦⑧⑨⑩⑪⑫⑬⑭⑯⑰⑱⑲⑳ⅠⅡⅢⅣⅤⅥⅦⅧ
ⅨⅩ㍉㌔㌢㍍㌘㌧㌃㌶㍑㍗㌍㌦㌣㌫㍊㌻㎜㎝㎞㎎㎏㏄㎡㍻〝〟
№㏍℡㊤㊥㊦㊧㊨㈱㈲㈹㍾㍽㍼∮∟⊿纊褜鍈銈蓜俉炻昱棈鋹曻
彅丨仡仼伀伃伹佖侒侊侚侔俍偀倢俿倞偆偰偂傔僴僘兊兤冝冾凬
刕劜劦勀勛匀匇匤卲厓厲叝﨎咜咊咩哿喆坙坥垬埈埇﨏塚增墲夋
奓奛奝奣妤妺孖寀甯寘寬尞岦岺峵崧嵓﨑嵂嵭嶸嶹巐弡弴彧德忞
恝悅悊惞惕愠惲愑愷愰憘戓抦揵摠撝擎敎昀昕昻昉昮昞昤晥晗晙
晴晳暙暠暲暿曺朎朗杦枻桒柀栁桄棏﨓楨﨔榘槢樰橫橆橳橾櫢櫤
毖氿汜沆汯泚洄涇浯涖涬淏淸淲淼渹湜渧渼溿澈澵濵瀅瀇瀨炅炫
焏焄煜煆煇凞燁燾犱犾猤猪獷玽珉珖珣珒琇珵琦琪琩琮瑢璉璟甁
畯皂皜皞皛皦益睆劯砡硎硤礰礼神祥禔福禛竑竧靖竫箞精絈絜綷
綠緖繒罇羡羽茁荢荿菇菶葈蒴蕓蕙蕫﨟薰蘒﨡蠇裵訒訷詹誧誾諟
諸諶譓譿賰賴贒赶﨣軏﨤逸遧郞都鄕鄧釚釗釞釭釮釤釥鈆鈐鈊鈺
鉀鈼鉎鉙鉑鈹鉧銧鉷鉸鋧鋗鋙鋐﨧鋕鋠鋓錥錡鋻﨨錞鋿錝錂鍰鍗
鎤鏆鏞鏸鐱鑅鑈閒隆﨩隝隯霳霻靃靍靏靑靕顗顥飯飼餧館馞驎髙
髜魵魲鮏鮱鮻鰀鵰鵫鶴鸙黑ⅰⅱⅲⅳⅴⅵⅶⅷⅸⅹ￢￤＇＂]' //依存文字
.')');

$tani_names = array();
$comp_names = array();
function getName($sname) {
	global $synonyms,$coldef;
	$name="";
	$sname=preg_replace("/(■|（訂正）|\n|\r)/u","",  $sname);
	if (array_key_exists($sname,$synonyms)) {
		$name=$synonyms[$sname];
	}
	else {
		if (array_key_exists($sname,$coldef)) {
		$name=$sname;
		}
		else $name="";
	}
	return $name;
}

function getVal($sname) {
	global $synonyms,$coldef;
	$name="";
	$sname=preg_replace("/(■|（訂正）|\n|\r)/u","",  $sname);
	if (array_key_exists($sname,$synonyms)) {
		$same=$synonyms[$sname];
	}
	if (array_key_exists($sname,$coldef)) {
		$val=$coldef[$sname]['val'];
	}
		else $val="";
	return $val;
}
function zen2han($str){
        $replace_of = array('ｳﾞ','ｶﾞ','ｷﾞ','ｸﾞ',
                            'ｹﾞ','ｺﾞ','ｻﾞ','ｼﾞ',
                            'ｽﾞ','ｾﾞ','ｿﾞ','ﾀﾞ',
                            'ﾁﾞ','ﾂﾞ','ﾃﾞ','ﾄﾞ',
                            'ﾊﾞ','ﾋﾞ','ﾌﾞ','ﾍﾞ',
                            'ﾎﾞ','ﾊﾟ','ﾋﾟ','ﾌﾟ','ﾍﾟ','ﾎﾟ');
        $replace_by = array('ヴ','ガ','ギ','グ',
                            'ゲ','ゴ','ザ','ジ',
                            'ズ','ゼ','ゾ','ダ',
                            'ヂ','ヅ','デ','ド',
                            'バ','ビ','ブ','ベ',
                            'ボ','パ','ピ','プ','ペ','ポ');
        $_result = str_replace($replace_by, $replace_of, $str);
        
        $replace_of = array('ｱ','ｲ','ｳ','ｴ','ｵ',
                            'ｶ','ｷ','ｸ','ｹ','ｺ',
                            'ｻ','ｼ','ｽ','ｾ','ｿ',
                            'ﾀ','ﾁ','ﾂ','ﾃ','ﾄ',
                            'ﾅ','ﾆ','ﾇ','ﾈ','ﾉ',
                            'ﾊ','ﾋ','ﾌ','ﾍ','ﾎ',
                            'ﾏ','ﾐ','ﾑ','ﾒ','ﾓ',
                            'ﾔ','ﾕ','ﾖ','ﾗ','ﾘ',
                            'ﾙ','ﾚ','ﾛ','ﾜ','ｦ',
                            'ﾝ','ｧ','ｨ','ｩ','ｪ',
                            'ｫ','ヵ','ヶ','ｬ','ｭ',
                            'ｮ','ｯ','､','｡','ｰ',
                            '｢','｣','ﾞ','ﾟ');
        $replace_by = array('ア','イ','ウ','エ','オ',
                            'カ','キ','ク','ケ','コ',
                            'サ','シ','ス','セ','ソ',
                            'タ','チ','ツ','テ','ト',
                            'ナ','ニ','ヌ','ネ','ノ',
                            'ハ','ヒ','フ','ヘ','ホ',
                            'マ','ミ','ム','メ','モ',
                            'ヤ','ユ','ヨ','ラ','リ',
                            'ル','レ','ロ','ワ','ヲ',
                            'ン','ァ','ィ','ゥ','ェ',
                            'ォ','ヶ','ヶ','ャ','ュ',
                            'ョ','ッ','、','。','ー',
                            '「','」','”','');        
        $_result = str_replace($replace_by, $replace_of, $_result);
        $replace_of = array('-','-',
                            '｢','｣','"','ﾟ');
        $replace_by = array('―',
			'－',
                            '「','」','”','');        

        $_result = str_replace($replace_by, $replace_of, $_result);

        $replace_of = array('A','B','C','D','E',
                            'F','G','H','I','J',
                            'K','L','M','N','O',
                            'P','Q','R','S','T',
                            'U','V','W','X','Y',
                            'Z');
        $replace_by = array('Ａ','Ｂ','Ｃ','Ｄ','Ｅ',
                            'Ｆ','Ｇ','Ｈ','Ｉ','Ｊ',
                            'Ｋ','Ｌ','Ｍ','Ｎ','Ｏ',
                            'Ｐ','Ｑ','Ｒ','Ｓ','Ｔ',
                            'Ｕ','Ｖ','Ｗ','Ｘ','Ｙ',
                            'Ｚ');
        $_result = str_replace($replace_by, $replace_of, $_result);
        $replace_of = array('a','b','c','d','e',
                            'f','g','h','i','j',
                            'k','l','m','n','o',
                            'p','q','r','s','t',
                            'u','v','w','x','y',
                            'z');
        $replace_by = array('ａ','ｂ','ｃ','ｄ','ｅ',
                            'ｆ','ｇ','ｈ','ｉ','ｊ',
                            'ｋ','ｌ','ｍ','ｎ','ｏ',
                            'ｐ','ｑ','ｒ','ｓ','ｔ',
                            'ｕ','ｖ','ｗ','ｘ','ｙ',
                            'ｚ');
        $_result = str_replace($replace_by, $replace_of, $_result);
        $replace_of = array('0','1','2','3','4',
                            '9','5','6','7','8');
        $replace_by = array('１','２','３','４','５',
                            '９','５','６','７','８');
        $_result = str_replace($replace_by, $replace_of, $_result);
        return $_result;
}
      //ライブラリ読み込み
	require_once '../phpexcel/Classes/PHPExcel.php';
	require_once '../phpexcel/Classes/PHPExcel/IOFactory.php';
  

if (isset($_REQUEST['page']))
$page = $_REQUEST['page'];  // 要求したページを取得
else $page=1;
if (isset($_REQUEST['limit']))
$limit = $_REQUEST['rows']; // グリッド内で使用したい列数を取得
else $limit=100;
if (isset($_REQUEST['sidx']))
$sidx = $_REQUEST['sidx']; // ユーザーがクリックして並べ替えを行うなど、インデックス列を取得
else $sidx=1;
if (isset($_REQUEST['sord']))
$sord = $_REQUEST['sord']; // 指示内容を取得
else $sord=1;
if(!$sidx) $sidx =1;
if(!$sord) $sord =1;




	$encoding=null;
      
	$response->rows = array();
	$cols=array();
	$csvdata=array();
	$colname=array();
	$start_data=false;  //データがはじまるとtrue
	$fixcols=false;     // 列情報を確定後true
	$csvtext="";

//                  array_push($response->rows,array("id"=>$j,"cell"=>array($no,$col1,$col2)));
	$j=1;
foreach($coldef as $key => $values){
	$cols=array();
	$cols[0]= $key;
	$s=array();
	$s=array_keys($synonyms, $cols[0]);
//	$cols = array_merge($cols, $s);
	$cols[1]= implode(",", $s);;
//var_dump($cols);
        $start = $limit*$page - $limit; // $limit * ($page - 1) にしない
        if ($start<0) $start = 0;
	if (($page*$limit>=$j)&&($j > $start))
		array_push($response->rows,array("id"=>$j,"cell"=>$cols));
	$j++;
}

	$response->records = $j-1;
	$response->page = $page;
	$response->total = ceil($j/$limit);

	echo json_encode($response);

?>

