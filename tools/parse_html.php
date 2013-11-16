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
$pkgHandle = mysql_real_escape_string($_GET['pkg']);
$bc = new PcShooterChListFavoritesBlockController();
$file = File::getByID($fileID);
$fv = $file->getRecentVersion();
$uploadFile = $fv->getDownloadURL();

$langNoJsLinks = t('No JavaScript-links allowed!');
$tableColumns = $bc->getBookmarkTableColumnsNames();
$jsonData = array();
$newArr = array();
$oldKey = null;
$newKey = null;
$dateFormat = $bc->getDateFormat();
/**
 * Sort $tableColumns like $buffer:
 * Using $db->getAssoc,
 * the db-column INFORMATION_SCHEMA.COLUMNS.ORDINAL_POSITION
 * is used as array-key
 *
 *          table columns:  $buffer:
 * url:     6               0
 * date:    3               1
 * icon:    4               2
 * text:    5               3
 *
 */
$colSortKeys = array(6, 3, 4, 5);

/**
 * Search & replace vars
 */
$href = '<DT><A HREF=';
$h3 = '<DT><H3';
$h3a = '</A>';
$h3e = '</H3>';
// <a> attributes
$lm[] = ' LAST_MODIFIED=';
$lm[] = ' ADD_DATE=';
$lm[] = ' ICON=';
$parseDelimiter = '@||@';

/**
 * Replace tags
 */
$str = str_replace($href, $parseDelimiter, file_get_contents($uploadFile));
$str = str_replace($h3, $parseDelimiter, $str);
//$str = str_replace($h3e, $parseDelimiter, $str);

$buffer = explode($parseDelimiter, $str);

/**
 * Cut off the first array entry: "Bookmarks"
 */
array_splice($buffer, 0, 1);

$i = 0;
while ($i < sizeof($buffer)) {
    $buffer[$i] = explode('"', $buffer[$i]);
    $j = 0;

    while ($j < sizeof($buffer[$i])) {
        // Deletes the <a> attributes
        if (in_array($buffer[$i][$j], $lm)) {
            array_splice($buffer[$i], $j, 1);
        }
        // Deletes empty entries
        if (empty($buffer[$i][$j]) || !isset($buffer[$i][$j]) || $buffer[$i][$j] == '' || $buffer[$i][$j][0] == ' ') {
            array_splice($buffer[$i], $j, 1);
        }
        // Deletes ">" at first place of string, don't ask me why it's there
        if ($buffer[$i][$j][0] == '>'){
            $buffer[$i][$j][0] = '';
        }
        // Disallow JS-links
        if (stripos($buffer[$i][$j], 'javascript:') !== false) {
            $jsonData['errorMsg'][] = $langNoJsLinks;
            $buffer[$i][$j] = $langNoJsLinks;
        }
        // Check, if it's a title
        if (strpos($buffer[$i][$j], '</H3>') !== false) {
            $newArr[$i][$tableColumns[$colSortKeys[$j]]] = trim(strip_tags('title_' . preg_replace('/[[:cntrl:]]/i', '', $buffer[$i][$j])));
        } else {
            $newArr[$i][$tableColumns[$colSortKeys[$j]]] = trim(strip_tags(preg_replace('/[[:cntrl:]]/i', '', $buffer[$i][$j])));
        }
        // Set a formatted date from timestamp
        if ($tableColumns[$colSortKeys[$j]] == 'btPcShooterChListFavoritesBookMarksDate') {
            //Log::addEntry($tableColumns[$colSortKeys[$j]]);
            $dtStr = date($dateFormat, intval($buffer[$i][$j]));
            $newArr[$i][$tableColumns[$colSortKeys[$j]]] = $dtStr;
        }
        $j++;
    }
    $j = 0;
    $counter = 0;
    $lastKey = 0;
    foreach ($tableColumns as $tableKey => $tableValue) {
        $k = 0;
        foreach ($newArr[$i] as $newArrKey => $newArrVal ) {
            $lastKey = $newArrKey;
            if ($newArrKey == $tableValue) {
                $counter++;
                break;
            }
            else {
                $counter = 0;
                $val = $newArr[$i][$lastKey];
                $newKey = $tableValue;
            }
            $k++;
        }
        if ($counter == 0) {
            $e = 'red';
            $newArr[$i][$newKey] = $val;
            $newArr[$i][$lastKey] = $blankImage;
        } else {
            $e = 'black';
        }

        $j++;
    }
    $i++;
}

$jsonData = $newArr;
// That's it. Send it back to the client & controller

$bc->setBookmarkData($newArr);
$realNew = $bc->getBookmarkDataRecords(PHP_INT_MAX);
echo json_encode($realNew);

exit;