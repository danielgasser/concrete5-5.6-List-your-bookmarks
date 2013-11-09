<?php
//TODO Sprach-Strings!!

defined('C5_EXECUTE') or die("Access Denied.");
$pkgHandle = mysql_real_escape_string($_GET['pkgHanlde']);
$uploadDir = DIR_BASE . '/files/tmp/' . $pkgHandle;

$jsonData = array();

/**
 * Upload directory check
 */
if (file_exists($pkgHandle) && is_dir($pkgHandle)) {
    $jsonData['errorMsg'][] = 'directory '. $pkgHandle.' OK';
} else {
    if(!mkdir($pkgHandle, 0777)){
        $jsonData['errorMsg'][] = 'directory creation failed (' . $pkgHandle . ')';
    } else {
        $jsonData['errorMsg'][] = 'directory ' . $pkgHandle . ' created.';
    }
    if (!chmod($pkgHandle, 0755)) {
        $jsonData['errorMsg'][] = 'permission set to 0777 on ' . $pkgHandle;
    } else {
        $jsonData['errorMsg'][] = 'permission settin to 0777 on ' . $pkgHandle . ' has failed';
    }
}

/**
 * Move the uploaded bookmark file check
 */
$uploadFile = strtolower($uploadDir . '/' . basename($_FILES['thafile']['name']));
if (move_uploaded_file(strtolower($_FILES['thafile']['tmp_name']), $uploadFile)) {
    $jsonData['errorMsg'][] = "Datei ist valide und wurde erfolgreich hochgeladen.\n";
} else {
    $jsonData['errorMsg'][] = "Möglicherweise eine Dateiupload-Attacke!\n";
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
            $jsonData['errorMsg'][] = t('No JavaScript-links allowed!');
            $buffer[$i][$j] = t('No JavaScript-links allowed!');
        }
        $j++;
    }
    $i++;
}

echo '<pre>Buffer: ';
print_r($buffer);
echo '</pre>';

$jsonData['data'] = $buffer;
// That's it. Send it back to the client
//echo json_encode($jsonData);
