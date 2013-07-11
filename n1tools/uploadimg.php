<?php
	
$width=null;
$height=null;
			
function createImage($size="S", $srcpath,$destpath, $filename){
	global $width,$height;
	if ($size=="S"){
		$headerchr="S";
		$swidth=80;
		$sheight=80;
	}
	else{
		$headerchr="L";
		$swidth=240;
		$sheight=240;
	}

	$image=null;
	if (preg_match('/\.jpe?g$/i', $filename)){
		$image = imagecreatefromjpeg($srcpath.$filename);
		$newname=$destpath.$headerchr.preg_replace('/\.jpe?g$/i','.JPG', $filename);
	}
	if (preg_match('/\.gif$/i', $filename)){
		$image = imagecreatefromgif($srcpath.$filename);
		$newname=$destpath.$headerchr.preg_replace('/\.gif$/i','.JPG', $filename);
	}
	if (preg_match('/\.bmp$/i', $filename)){
		$image = imagecreatefrombmp($srcpath.$filename);
		$newname=$destpath.$headerchr.preg_replace('/\.bmp$/i','.JPG', $filename);
	}
	if (preg_match('/\.png$/i', $filename)){
		$image = imagecreatefrompng($srcpath.$filename);
		$newname=$destpath.$headerchr.preg_replace('/\.png$/i','.JPG', $filename);
	}
	if (is_null($image)) { error_log("not proper file : ".$filename); return null; } 

	$width = ImageSx($image);
	$height = ImageSy($image);
	$startx=0;
	$starty=0;
//	if ($width > $height) { $startx=floor(($width-$height)/2); $width=$height; }
//	if ($height > $width) { $starty=floor(($height-$width)/2); $height=$width; }
	if ($width > $height) { 
		$starty=-floor(($width-$height)/2); $height=$width; }
	if ($height > $width) { $startx=-floor(($height-$width)/2); $width=$height; }

	$simage = ImageCreateTrueColor($swidth, $sheight);
	$whitecolor = imagecolorallocate($simage, 255, 255, 255);
//	imagefill($image, 0, 0, $whitecolor);
	imagefill($simage,0,0,$whitecolor);
//	imagecolortransparent($simage, $whitecolor);

	$resizerate=$swidth/$width;
	error_log("resizerate=".$resizerate,0);
	ImageCopyResampled($simage, $image, 0,0,$startx,$starty, $swidth, $sheight, $width, $height);

	if ($startx < 0) { 
		imagefilledrectangle($simage,0,0,floor(-$startx*$resizerate),$sheight, $whitecolor);
		error_log("Xrectacngle1=0/0-".(floor(-$startx*$resizerate))."/".$sheight,0);
		imagefilledrectangle($simage,$swidth+floor($startx*$resizerate),0,$swidth,$sheight, $whitecolor);
		error_log("Xrectacngle2=".($swidth+floor($startx*$resizerate))."/0-".$swidth."/".$sheight,0);
//		imagefill($simage, 0, 0, $whitecolor);
//		imagefill($simage, $swidth-1, 0, $whitecolor);
	}
	if ($starty < 0) { 
		imagefilledrectangle($simage,0,0,$swidth, floor(-$starty*$resizerate), $whitecolor);
		error_log("Yrectacngle1=0/0-".$swidth."/".(floor(-$starty*$resizerate)),0);
		imagefilledrectangle($simage,0,$sheight+floor($starty*$resizerate),$swidth,$sheight, $whitecolor);
		error_log("Yrectacngle2=0/".($sheight+floor($starty*$resizerate))."-".$swidth."/".$sheight,0);
//		imagefill($simage, 0,0,  $whitecolor);
//		imagefill($simage, 0,$sheight-1,  $whitecolor);
	}
	imagejpeg($simage,$newname,100);
	imagedestroy($simage);
	return $newname;
}
						
	$error = "";
	$fileElementName = $_POST['file_element'];   
	$folder = "./upload";
		$mkdir = false;
	$smallimg="";
	$largeimg="";
	$zip="";
	$size=0;

	if (isset($_POST['folder'])){
		$folder = $_POST['folder'];   
		exec("mkdir ".$folder);
		$mkdir = true;

	$fnam = $_FILES[$fileElementName]['name'];
	$jpgpath = $folder."/";
	$srcpath=preg_replace("/[0-9]+$/", "", $folder);   // 後ろの数字を取る
	}
	else { error_log("NO folder Parameter.",0); return 0;} 
			
	
	
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

		error_log("NOT ERROR src_path=".$srcpath,0);

//		$path = $folder;
		$size = @filesize($_FILES[$fileElementName]['tmp_name']);
			
		//for security reason, we force to remove uploaded file
		//Use move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $destination) instead.

		move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $srcpath.basename($fnam));

		$img_type = $_FILES[$fileElementName]["type"];
		error_log("NOT ERROR JPG_PATH=".$jpgpath,0);
//		if ( strstr($img_type,"zip")) exec("unzip ".$fullpath." -d ".$jpgpath);
			//@unlink($_FILES[$fileElementName]['tmp_name']);
		if (preg_match("/(jpg|jpeg|gif|bmp|png)/i",$img_type)) {
		$smallimg=createImage("S", $srcpath, $jpgpath, basename($fnam));
		$largeimg=createImage("L", $srcpath, $jpgpath, basename($fnam));
		error_log("SRC =".$srcpath.basename($fnam).":".$fnam,0);
		exec("rmdir ".$folder);  // もし空だったら削除、中身があったら削除しない
//	exec("rm ".$path.basename($fnam));
		exec("zip -r ".$folder.".zip ".$folder);
		$zip= $folder.".zip";
		}
	}
/*
	if ($mkdir) {
	exec("rmdir ".$folder);
//	exec("rm ".$path.basename($fnam));
	exec("zip -r ".$folder.".zip ".$folder);
	$zip= $folder.".zip";
	}
*/
	if ($error)	
		error_log("ERROR=".$error,0);
	$res = new stdClass();
				
	$res->error = $error;
	$res->filename = $fnam;
	$res->path = $jpgpath;
	$res->size = sprintf("%.2fMB", $size/1048576);
	$res->dt = date('Y-m-d H:i:s');
	if (!empty($smallimg)) $res->small = $smallimg;
	if (!empty($zip)) $res->zip = $zip;
	$res->src = $srcpath.basename($fnam);
	if (!empty($largeimg)) $res->large = $largeimg;
	echo json_encode($res);	
	
?>
