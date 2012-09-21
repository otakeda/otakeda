<?php
ob_start();
$filename = basename($_FILES['img_path']['name']);
if (move_uploaded_file($_FILES['img_path']['tmp_name'], './upload/' . $filename)) {
    $data = array('filename' => $filename);
} else {
    $data = array('error' => 'Failed to save');
}

header('Content-type: text/html');
echo json_encode($data);

$content=ob_get_contents();
ob_end_clean();
echo $content;

?>
