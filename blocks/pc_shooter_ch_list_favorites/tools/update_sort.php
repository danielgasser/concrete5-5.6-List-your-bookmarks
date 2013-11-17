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
$bc = new PcShooterChListFavoritesBlockController();
$db = Loader::db();
$bookmarkID = mysql_real_escape_string($_POST['bookmarkID']);
$arr = $_POST['sortage'];
$i = 0;
$queryStr = 'UPDATE btPcShooterChListFavoritesBookMarks SET Zsort = CASE' . "\n";
while($i < sizeof($arr)){
    $queryStr .= 'WHEN bookmarkID = ' . mysql_real_escape_string($arr[$i]['bid']) .' THEN ' . mysql_real_escape_string($arr[$i]['vale']) . "\n";
    $i++;
}
$queryStr .= ' END';
$db->Execute($queryStr);
exit;