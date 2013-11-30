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

$fileID = mysql_real_escape_string($_GET['fileID']);
$file = File::getByID($fileID);
$fv = $file->getRecentVersion();
$uploadFile = $fv->getRelativePath(true);

$bc = new PcShooterChListFavoritesBlockController();

$blankImage = $bc->blankImage;

$dateFormat = $bc->getDateFormat();

$html = file_get_contents($uploadFile);

$dom = new DOMDocument;

@$dom->loadHTMLFile($uploadFile);
$dom->preserveWhiteSpace = false;
/**
 * $tableColumns = array_values($bc->getBookmarkTableColumnsNames());
 *     [0] => btPcShooterChListFavoritesBookMarksDate
 *     [1] => btPcShooterChListFavoritesBookMarksIcon
 *     [2] => btPcShooterChListFavoritesBookMarksText
 *     [3] => btPcShooterChListFavoritesBookMarksUrl
 *     [4] => btPcShooterChListFavoritesBookMarksLevel
 *     [5] => btPcShooterChListFavoritesBookMarksSort

 */
$xpath = new DOMXPath($dom);
$unusedTags = $xpath->query('//meta | //title | //body | //h1 | //hr');
$unusedLen = $unusedTags->length;

$tagObj = $xpath->query('//dt//h3 | //dt//a');
$len = $tagObj->length ;
$depth = 0;
$data = array();
$noJS = t('No JavaScript-links allowed!');

$nodeZero = getDepth($tagObj->item(0));
$depth = 0;
for ($i = 0; $i < $len; $i++) {
    $date =date($dateFormat, $tagObj->item($i)->getAttribute('add_date'));
    $data[$i]['btPcShooterChListFavoritesBookMarksDate'] = $date;//$tagObj->item($i)->getAttribute('add_date');
    $data[$i]['btPcShooterChListFavoritesBookMarksIcon'] = ($tagObj->item($i)->getAttribute('icon') == '') ? $blankImage : $tagObj->item($i)->getAttribute('icon');
    $data[$i]['btPcShooterChListFavoritesBookMarksIsTitle'] = ($tagObj->item($i)->tagName == 'h3') ? true : false;
    $data[$i]['btPcShooterChListFavoritesBookMarksKeyWord'] = ($tagObj->item($i)->getAttribute('shortcuturl')) == '' ? 'no-keyword' : $tagObj->item($i)->getAttribute('shortcuturl');
    $depth = substr_count($tagObj->item($i)->getNodePath(), 'dl');
    if ($tagObj->item($i)->tagName == 'h3') {
        $data[$i]['btPcShooterChListFavoritesBookMarksLevel'] = $depth;
    } else {
        $data[$i]['btPcShooterChListFavoritesBookMarksLevel'] = $oldDepth;
    }
    $data[$i]['btPcShooterChListFavoritesBookMarksLevel'] = $depth;
    $data[$i]['btPcShooterChListFavoritesBookMarksSort'] = $i;
    $data[$i]['btPcShooterChListFavoritesBookMarksUrl'] = ($tagObj->item($i)->getAttribute('href') == '') ? 'no-url' : $tagObj->item($i)->getAttribute('href');
    $data[$i]['btPcShooterChListFavoritesBookMarksText'] = $tagObj->item($i)->nodeValue;
    $oldDepth = $depth;
}

function getDepth($node) {
    $d = 0;
   // echo $node->tagName . "\n";
    while ($node != null) {
        $node = $node->parentNode;
        $d++;
    }
    return $d;
}

$bc->setBookmarkData($data);
$realNew = $bc->getBookmarkDataRecords(PHP_INT_MAX);

echo json_encode($realNew);

exit;