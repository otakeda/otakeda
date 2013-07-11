<?php
	
header("Content-Type: application/json; charset=utf-8");
$error = "";
$fileElementName = $_POST['file_element'];   
$folder = $_POST['folder'];   
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

	$path = $folder;
	$size = @filesize($_FILES[$fileElementName]['tmp_name']);
			
			//for security reason, we force to remove uploaded file
	$destination=$path.$fnam;
	move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $destination) ;
        if ( $extension=="zip") {exec("unzip ".$destination." -d ".$path  );
	error_log("unzip ".$destination." -d ".$path,0);
	}
	else error_log("ext:".$extension,0);
//			@unlink($_FILES[$fileElementName]['tmp_name']);
						
}
	
$res = new stdClass();
$res->error = $error;
$res->filename = $fnam;
$res->path = $path;
$res->fletype = $extension;
$res->size = sprintf("%.2fMB", $size/1048576);
$res->dt = date('Y-m-d H:i:s');
echo json_encode($res);	
//error_log("res:".json_encode($res),0);
?>
