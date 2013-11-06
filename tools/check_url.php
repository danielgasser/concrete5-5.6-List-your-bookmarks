<?php
defined('C5_EXECUTE') or die("Access Denied.");

$file = $_POST['bookMark'];
$check = @get_headers($file, 1);

echo json_encode($check);
