<?php
/**
 * List Your Bookmarks Tool file: parses bookmark file
 * @author This addon creates a list of indiviual blocks,<br>with your bookmarks in it.
<ul><li>Export the Bookmarks from your browser(s)</li>
<li>Import your bookmarks into a list.</li>
<li>Add a small text and an image to each of the bookmarks.</li>
<li>Edit each bookmark like any normal block.</li>
<li>Each whole block is a link to another website.
 * @version 0.1
 * @package List Your Bookmarks Tool file: parses bookmark file
 */


defined('C5_EXECUTE') or die("Access Denied.");
$arr = $_POST['bookmarkIDs'];
if (sizeof($arr) == 0 || !isset($arr) || empty($arr)) exit;

$bc = new PcShooterChListFavoritesBlockController();
$db = Loader::db();
$bID = (isset($bc->bID)) ? $bc->bID : PHP_INT_MAX;
//print_r($arr);
$i = 0;
$queryStr = 'DELETE FROM btPcShooterChListFavoritesBookMarks WHERE ' . "\n";
while($i < sizeof($arr)){
    $queryStr .= 'bookmarkID = ' . mysql_real_escape_string($arr[$i]['bookmarkID']) .' OR ' . "\n";
    $i++;
}
$queryStr = substr($queryStr, 0, -4);
//print $queryStr;
$db->Execute($queryStr);
$bc->getBookmarkDataRecords($bID);
echo json_encode($bc->getBookmarkDataRecords($bID));
exit;