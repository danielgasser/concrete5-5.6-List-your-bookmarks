<?php
defined('C5_EXECUTE') or die("Access Denied.");
include('constants.php');
echo '<pre>';
$b = Block::GetById(intval($_GET['bID']));
$blankImage = str_replace(' ', '', $_GET['bImg']);
$bc = new PcShooterChListFavoritesBlockController($b);
$pkgHandle = mysql_real_escape_string($_GET['pkgHandle']);
$uploadDir = DIR_BASE . '/files/tmp/' . $pkgHandle;
$langNoJsLinks = t('No JavaScript-links allowed!');
$tableColumns = $bc->getBookmarkTableColumnsNames();
print_r($tableColumns);
$jsonData = array();
$newArr = array();
$oldKey = null;
$newKey = null;
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
 * Upload directory check
 */
if (file_exists($pkgHandle) && is_dir($pkgHandle)) {
    $jsonData['errorMsg'][] = t('Directory') . ' ' . $pkgHandle.' ' . t('exists');
} else {
    if(!mkdir($pkgHandle, 0777)){
        $jsonData['errorMsg'][] = t('Directory') . ' (' . $pkgHandle . ') ' . t('creation failed');
    } else {
        $jsonData['errorMsg'][] = t('Directory') . ' ' . $pkgHandle . t('successfully created');
    }
    if (!chmod($pkgHandle, 0755)) {
        $jsonData['errorMsg'][] = t('Permissions set to') . ' ' . '0777' . ' ' . t('on') . ' ' . $pkgHandle;
    } else {
        $jsonData['errorMsg'][] = t('Setting permissions to') . ' ' . '0777' . ' ' . t('on') . ' ' . $pkgHandle . ' ' . t('has failed');
    }
}

/**
 * Move the uploaded bookmark file check
 */
$uploadFile = strtolower($uploadDir . '/' . basename($_FILES['thafile']['name']));
if (move_uploaded_file(strtolower($_FILES['thafile']['tmp_name']), $uploadFile)) {
    $jsonData['errorMsg'][] = t('File is valid and has been successfully uploaded to') . ' ' . $uploadDir;//"Datei ist valide und wurde erfolgreich hochgeladen";
} else {
    $jsonData['errorMsg'][] = t('Possible file-upload attack') . '!';//"Möglicherweise eine Dateiupload-Attacke";
}

/**
 * Search & replace vars
 */
$href = '<DT><A HREF=';
$h3 = '<DT><H3';
// <a> attributes
$lm[] = '  LAST_MODIFIED=';
$lm[] = ' ADD_DATE=';
$lm[] = ' ICON=';
$parseDelimiter = '@||@';

/**
 * Replace tags
 */
$str = str_replace($href, $parseDelimiter, file_get_contents($uploadFile));
$str = str_replace($h3, $parseDelimiter, $str);
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
        $newArr[$i][$tableColumns[$colSortKeys[$j]]] = $buffer[$i][$j];
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
                //echo 'thumbsup: ' . $newArrKey . ' == ' . $tableValue . '<br>';
                $counter++;
                break;
            }
            else {
                //echo 'fuckit: ' . $newArrKey . ' NOT ' . $tableValue . '<br>';
                $counter = 0;
               //echo '<br>this ist '. $k . 'val: ';

                $val = $newArr[$i][$lastKey];
                $newKey = $tableValue;
            }
            $k++;
        }
        if ($counter == 0) {
            $e = 'red';
            $newArr[$i][$newKey] = $val;
            $newArr[$i][$lastKey] = $blankImage;
            //echo 'key(): ' . key($tableColumns);
            //$newArr[$i][$oldKey] = $newArr[$i][$newKey];
            //unset($newArr[$i][$oldKey]);
        }else
            $e = 'black';

        //$newArr[$i]
        //echo ' c: '. $tableValue.' : ' . $newArrVal . ' <span style="color: ' . $e . '">' . $counter . '</span><hr>';
        $j++;
    }

    $bufK = array_keys($newArr[$i]);
    //if (sizeof($buffer[$i]) < sizeof($tableColumns)) {
    //   // echo 'leeheer!';
    //    array_push($newArr[2],  'essmpty');
    //}
    ////

    $i++;
}

print_r($newArr);
echo '</pre>';

$jsonData['data'] = $buffer;
// That's it. Send it back to the client
//echo json_encode($jsonData);
