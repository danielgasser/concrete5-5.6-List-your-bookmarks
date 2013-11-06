<?php
defined('C5_EXECUTE') or die("Access Denied.");
$file = $_POST['bookMark'];

echo json_encode(array('ex' => file_exists($file), 'f' => $file));
exit;
if ($file_headers[0] == 'HTTP/1.1 404 Not Found') {
    echo json_encode(t('This bookmark is not a valid URL.'));
} else {
    echo json_encode(t('This bookmark is valid.'));
}
exit;
