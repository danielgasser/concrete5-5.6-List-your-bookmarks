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

$bookmarkID = mysql_real_escape_string($_POST['bookmarkID']);
// That's it. Send it back to the client & controller
echo '<pre>';
print_r($_POST['fieldValues']);
echo '</pre>';
$result = $bc->updateBookmarksByID($bookmarkID, $_POST['fieldValues']);
echo json_encode($result);

exit;