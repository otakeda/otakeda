 <?php
require_once 'coldef.php';


define ('KINSHI_CHK', '[\t\n\r\'"]');
define ('KINSHIZEN_CHK', '[―－‐∥”’
]');
define ('HANKANA_CHK', '[ｱ-ﾝﾞﾟｧ-ｫｬ-ｮｰ｡]');
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
function getTommorow(){
	 return date('Y/m/d', strtotime('+1 day')); 
}
function getTaniArray(&$t){
	global $tani_names;
	$tani_names['個']='001';	
	$tani_names['本']='002';	
}
function getTaniCd($tname){
	global $tani_names;
	$cd="";
	if (array_key_exists($tname,$tani_names)) {
		$cd=$tani_names[$tname];
	}
	else {
	$cd="***";
	}
	return $cd;
}
function getCompanyCd($cname){
	global $comp_names;
	$cd="";
	if (array_key_exists($cname,$comp_names)) {
		$cd=$comp_names[$cname];
	}
	else {
	$cd="***";
	}
	return $cd;
}
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
  
$page = $_REQUEST['page'];  // 要求したページを取得
$limit = $_REQUEST['rows']; // グリッド内で使用したい列数を取得
$sidx = $_REQUEST['sidx']; // ユーザーがクリックして並べ替えを行うなど、インデックス列を取得
$sord = $_REQUEST['sord']; // 指示内容を取得
if(!$sidx) $sidx =1;
$zipfile="";
$imgfiles=array();
if(isset($_REQUEST['zipfile'])){ 
	$zipfile=$_REQUEST{'zipfile'};
	$no_ext = pathinfo( $zipfile, PATHINFO_FILENAME);
	$readdir="./upload/".$no_ext;
	$imgfiles = scandir($readdir);
	error_log("dir=".$readdir.":".$imgfiles,0);
} else $zipfile="";
$makecsv=false;

if($filename=$_REQUEST{'filename'}){

	$extension = pathinfo( $filename, PATHINFO_EXTENSION);
	if ($extension=="xls")
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	else
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	$book = $objReader->load("./upload/".$filename);
      
         // 一番左のシート
	$book->setActiveSheetIndex(0);
	$sheet = $book->getActiveSheet();

	$encoding=null;
      
	$response->rows = array();
	$cols=array();
	$hinbans=array();
	$gazous=array();
	$kingakus=array();
	$sus=array();
	$stexts=array();
	$ltexts=array();
	$csvdata=array();
	$colname=array();
	$start_data=false;  //データがはじまるとtrue
	$fixcols=false;     // 列情報を確定後true
	$csvtext="";
	$chks=array();
	$matches=array();

	$rownum=0;   // Excel内行数
for($j = 1; $j <= 300; $j++){   //Tablrに表示している行数
	$rownum++;
	$colerror=false;
	for($i = 0; $i < 40; $i++){
		$cerror=false;
		$objCell = $sheet->getCellByColumnAndRow($i, $rownum); //col,rowの並び
		$cols[$i]=_getText($objCell);

// 10行目以降で1列目の値がなかったらbreak
		if (($i==0)&&(!$cols[$i])&&($rownum>9)) break;

//列確定 & データも開始
		if ($start_data&&$fixcols){
// fixcols = true で、colnameが入っているはず   coldefに列名が入っているかどうか
//			if (array_key_exists($colname[$i],$coldef)) {
			if (!($colname[$i]=="")){
			if (($cols[$i])&&($i>0)) { 
				$coldef[$colname[$i]]['val']=$cols[$i];
//			error_log($i."/".$j."  colname:".$colname[$i]."  coldef:".$coldef[$colname[$i]]['val'],0);
			}
			else
				$coldef[$colname[$i]]['val']="";
			}
			
//			if (array_key_exists($colname[$i],$coldef)) {
			if ((!$cerror)&&(preg_match("/".KINSHI_CHK."/u",$cols[$i]))){
				$cols[$i]="<span style=\"background:magenta;\">".strip_tags($cols[$i])."</span>";
				$colerror=true;
				$cerror=true;
			}
			if ((!$cerror)&&(preg_match("/".KINSHIZEN_CHK."/u",$cols[$i]))){
				$cols[$i]="<span style=\"background:green;\">".strip_tags($cols[$i])."</span>";
				$colerror=true;
				$cerror=true;
			}
			if ((!$cerror)&&(preg_match("/".IZON_CHK."/u",$cols[$i]))){
				$cols[$i]="<span style=\"background:red;\">".strip_tags($cols[$i])."</span>";
				$colerror=true;
				$cerror=true;
			}
			if ((!$cerror)&&(preg_match("/".HANKANA_CHK."/u",$cols[$i]))){
				$cols[$i]="<span style=\"background:cyan;\">".strip_tags($cols[$i])."</span>";
				$colerror=true;
				$cerror=true;
			}

			if (array_key_exists($i,$chks)) {
			if ((!$cerror)&&(!preg_match("/".$chks[$i]."/u",$cols[$i]))){
				if (!$cols[$i]) $cols[$i]="*";
				$cols[$i]="<span style=\"background:yellow;\">".$cols[$i]."</span>";
				$colerror=true;
				$cerror=true;
			}
			else
			if (!$cerror)
				$cols[$i]="<span style=\"background:gray;\">".$cols[$i]."</span>";
			}
			if ($zipfile&&(preg_match("/[a-zA-Z0-9\-._()\/]+\.JPG/",$cols[$i], $matches))){
				if (in_array($matches[0], $imgfiles))
				$cols[$i]="<span style=\"color:blue;\">".strip_tags($cols[$i])."</span>";
				else{
				$cols[$i]="<span style=\"color:red;\">".strip_tags($cols[$i])."</span>";
				$colerror=true;
				}
			}
		}
		else{
			if (!$fixcols){
//			$colname[$i]=preg_replace("/(■|（訂正）|\n|\r)/u","",  $cols[$i]);
			$colname[$i]=getName($cols[$i]);

//		error_log('COLNAME:'.$colname[$i],0);
			if (!($colname[$i]=="")){
//			if (array_key_exists($colname[$i], $coldef)){
				$cols[$i]="<span style=\"color:blue;\"> ".$cols[$i]."</span>";
				$chks[$i]=$coldef[$colname[$i]]['chk'];
				$cols[$i] .="<img src='chat.png' title='". $chks[$i]."'>";
		//	$coldef[(string)$colname]['pos']=$i;
			}
			
			}
		}
		switch ($i){
                        case 0:  // No
				if ($cols[$i] > 0)  $start_data=true;
		}

	} //列ループの終わり

// 10行目以降で1列目の値がなかったらbreak
	if (($i==0)&&(!$cols[$i])&&($rownum>9)) break;

	$start = $limit*$page - $limit; // $limit * ($page - 1) にしない
	if ($start<0) $start = 0;
//                  array_push($response->rows,array("id"=>$j,"cell"=>array($no,$col1,$col2)));
	if (($page*$limit>=$j)&&($j > $start)){
//		if ($colerror||$rownum<=10){    //エラーがあったか、10行目未満
		array_push($response->rows,array("id"=>$j,"cell"=>$cols));
//		}
//		else{  //10行目以降のエラーなし行は表示しない
//			$j--;
//		}  
	}

// CSV生成
	if ($start_data&&$makecsv){
		$k=0;
//		foreach($csvdef as $cd){
		foreach($csvdef as $key => $values){
			if ($k > 0) $csvtext.=",";
			$val = ($values['col']) ?  ($values['col']) : ($values['def']);
			if (strpos($val,'unc:')>0) {    // func:
				$fc=array();
				$fc=split(":", $val);
				if (function_exists($fc[1])) {
//					if (count($fc)==2&& array_key_exists($fc[2], $coldef)){
					if (count($fc)==3){
					$p = getVal($fc[2]) ;
					$val=$fc[1]($p);
//					error_log("FUNCTION $fc[1] : $fc[2] = $val",0);
//					error_log("FUNCTION $fc[1] : $p = $val",0);
					}
					else $val=$fc[1]();
				}
			}
			if (strpos($val,'ef:')>0) {    // ref:
				$fc=split(":", $val);
//				if (array_key_exists($fc[1], $coldef)){
				$val = getVal($fc[1]) ;
//					error_log("REF $fc[1] = $val",0);
			}
			if (!$val) $val=getVal($key) ;

//				$val = ($coldef[$cd]['val']) ?  ($coldef[$cd]['val']) : ($coldef[$cd]['def']);
//				error_log("VAL=". $val,0);
//				error_log("F=". $val,0);
//				error_log("VAL=". $val.$fc[0].$fc[1]. $fc[2],0);
			$csvtext.="\"".$val."\"";
			$k++;
		}
		$csvtext.="<br>\r\n";
	}

	if (count($chks) > 5)  $fixcols=true;
//		if ($j==6)	error_log(implode(",",$colname),0);
//		error_log("csvtext:".$csvtext,0);

}  //行ループのおわり

	$response->records = $j-1;
	$response->page = $page;
//	$response->total = ceil($j/$limit);
	$response->total = ceil($rownum/$limit);

/*
        foreach ($sheet->getRowIterator() as $row) {
                error_log ('    Row number - ' . $row->getRowIndex() ,0);

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
$i++;
                $response->rows[$i]['id']=$i;
                foreach ($cellIterator as $cell) {
                $response->rows[$i]['cell']=_getText($cell);
                        }
        }
*/
	echo json_encode($response);

	if ($makecsv){
	$fp = fopen("./upload/csvfile.csv","w");
	fwrite($fp, $csvtext);
	fclose($fp);
	}
}


  
      /**
       * 指定したセルの文字列を取得する
       *
       * 色づけされたセルなどは cell->getValue()で文字列のみが取得できない
       * また、複数の配列に文字列データが分割されてしまうので、その部分も連結して返す
       *
       *
       * @param $objCell Cellオブジェクト
       */ 
      function _getText($objCell = null)
      {
          if (is_null($objCell)) {
              return false;
          }
          $txtCell = "";
          //まずはgetValue()を実行
          $valueCell = $objCell->getValue();
          if (is_object($valueCell)) {
              //オブジェクトが返ってきたら、リッチテキスト要素を取得
              $rtfCell = $valueCell->getRichTextElements();
              //配列で返ってくるので、そこからさらに文字列を抽出
              $txtParts = array();
              foreach ($rtfCell as $v) {
                  $txtParts[] = $v->getText();
              }
              //連結する
              $txtCell = implode("", $txtParts);
          } else {
              if (!empty($valueCell)) {
                  $txtCell = $valueCell;
              }
          }
          return $txtCell;
      }
  ?>

