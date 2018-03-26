<?php
require(dirname(__FILE__) . '/../../Uploader.php');

// Directory where we're storing uploaded images
// Remember to set correct permissions or it won't work
$upload_dir = '/upload_files/';

$uploader = new FileUpload('uploadfile');

// Handle the upload
$result = $uploader->handleUpload($upload_dir);

if (!$result) {
  exit(json_encode(['success' => false, 'msg' => $uploader->getErrorMsg()]));
}

echo json_encode(['success' => true]);
