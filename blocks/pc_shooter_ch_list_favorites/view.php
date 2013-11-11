<?php defined('C5_EXECUTE') or die("Access Denied.");
echo '<div style="border: 1px solid #66aa33">';
echo '<pre>btPcShooterChListFavoritesBlockText: ';
//$my = $controller->getbtPcShooterChListFavoritesBlockText();
echo $btPcShooterChListFavoritesBlockText;
echo '<hr>bID: ';
echo $bID;
echo '<hr>bookMarkData: ';
foreach($bookMarkData as $key => $val){
    echo 'key: '.$key;
    echo '<br>val: '.print_r($val).'<hr>';
}
echo '<hr>btPcShooterChListFavoritesText: ';
print_r($btPcShooterChListFavoritesText);
echo '</pre></div>';