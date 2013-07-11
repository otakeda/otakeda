<?php
	
header("Content-Type: application/json; charset=utf-8");
$error = "";
$fileElementName = $_POST['file_element'];   
$folder = $_POST['folder'];   
error_log("folder:".$folder,0);
$matches = array();
$login_group="";
$before_login_group="";
$user_cd="";
$company_cd="";
$before_company_cd="";
$path="";
$record_date="";
if (preg_match("/(^[\.a-zA-Z0-9\/]+)/", $folder, $matches)) {
	$path=$matches[0];
//	exec("rm -rf ".$dirname);
error_log("path:".$path,0);
	exec("mkdir ".$path);
}
else {
	error_log("CANNOT TAKE pathname!!!",0);
return 0;
}

$srcpath=preg_replace("/[0-9]+$/", "", $path);   // 後ろの数字を取る
 
if (preg_match("/(blg=[a-zA-Z0-9]+)/", $folder, $matches)) {
	$before_login_group = str_replace("blg=","", $matches[0]);
}
if (preg_match("/(lg=[a-zA-Z0-9]+)/", $folder, $matches)) {
	$login_group = str_replace("lg=","", $matches[0]);
}
if (preg_match("/(uc=[a-zA-Z0-9]+)/", $folder, $matches)) {
	$user_cd = str_replace("uc=","", $matches[0]);
}
if (preg_match("/(bcc=[a-zA-Z0-9]+)/", $folder, $matches)) {
	$before_company_cd = str_replace("bcc=","", $matches[0]);
}
if (preg_match("/(cc=[a-zA-Z0-9]+)/", $folder, $matches)) {
	$company_cd = str_replace("cc=","", $matches[0]);
}
if (preg_match("/(rd=[0-9:|\-]+)/", $folder, $matches)) {
	$record_date = str_replace("rd=","", $matches[0]);
	$record_date = str_replace("-","/", $record_date);
}
if (preg_match("/(sd=[0-9:|\-]+)/", $folder, $matches)) {
	$shime_date = str_replace("sd=","", $matches[0]);
	$shime_date = str_replace("-","/", $shime_date);
}
error_log("bcc:".$before_company_cd);
error_log("cc:".$company_cd);
error_log("uc:".$user_cd);
$destination=null;
	
if(!empty($_FILES[$fileElementName]['error']))
{
	switch($_FILES[$fileElementName]['error'])
	{

		case '1':
			$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
			break;
		case '2':
			$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
			break;
		case '3':
			$error = 'The uploaded file was only partially uploaded';
			break;
		case '4':
			$error = 'No file was uploaded.';
			break;
		case '6':
			$error = 'Missing a temporary folder';
			break;
		case '7':
			$error = 'Failed to write file to disk';
			break;
		case '8':
			$error = 'File upload stopped by extension';
			break;
		case '999':
		default:
			$error = 'No error code avaiable';
	}
}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
{
	$error = 'No file was uploaded..';
}else 
{
			
	$fnam = $_FILES[$fileElementName]['name'];
	$type = $_FILES[$fileElementName]['type'];
	$tmp_ary = explode('.', $fnam);
	$extension = $tmp_ary[count($tmp_ary)-1];

	$size = @filesize($_FILES[$fileElementName]['tmp_name']);
			
			//for security reason, we force to remove uploaded file
	$destination=$path."/".$fnam;
	$fp = fopen($_FILES[$fileElementName]['tmp_name'],"r");
	$ofp = fopen($destination,"w");
//	flock($ofp,EX_LOCK);
	while (! feof ($fp)) {
		$load = fgets ($fp);
		if (preg_match("/".$before_company_cd."/", $load)){
		$load=str_replace($before_login_group,$login_group, $load);
		$load=str_replace($before_company_cd,$company_cd, $load);
//		$load=preg_replace("/[a-z]{3,30},201[0-9]\/[0-9]{1,2}\/[0-9]{1,2}/",$user_cd.",".date("Y/m/d"), $load);
		$load=preg_replace("/[a-z]{3,30}\",201[0-9]\/[0-9]{1,2}\/[0-9]{1,2}/",$user_cd."\",".$record_date, $load);
		$load=preg_replace("/[a-z]{3,30},201[0-9]\/[0-9]{1,2}\/[0-9]{1,2}/",$user_cd.",".$record_date, $load);
		if ($fnam=="NOM_KAIKEI_SHIME_SCHEDULE.csv"){
		$load=preg_replace("/,\"2\",201[0-9]\/[0-9]{1,2}\/[0-9]{1,2}/",",\"2\",".$shime_date,$load);
		$load=preg_replace("/,\"5\",201[0-9]\/[0-9]{1,2}\/[0-9]{1,2}/",",\"5\",".$shime_date,$load);
		}
		
//		error_log( "line:".$load,0);
//		if (strpos($load, $company_cd)>=0){
//		error_log( "strpos:".strpos($load, $company_cd),0);
		fputs($ofp,$load);
		}
		else
		error_log( "no:".$before_company_cd,0);
	}
	fclose($fp);
	fclose($ofp);
	move_uploaded_file($_FILES[$fileElementName]['tmp_name'],  $srcpath.basename($fnam)) ;
	exec("rmdir ".$path);  // もし空だったら削除、中身があったら削除しない

        exec("zip -r ".$path.".zip "
.$path);
        $zip= $path.".zip";
						
}
	
$res = new stdClass();
$res->error = $error;
$res->filename = $fnam;
$res->path = $path;
$res->fletype = $extension;
$res->size = sprintf("%.2fMB", $size/1048576);
$res->dt = date('Y-m-d H:i:s');
if (!empty($zip)) $res->zip = $zip;
$res->src = $srcpath.basename($fnam);
$res->dest = $destination;
 
echo json_encode($res);	
//error_log("res:".json_encode($res),0);
?>
