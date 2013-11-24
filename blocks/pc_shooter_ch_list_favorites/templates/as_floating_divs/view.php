<?php
/**
 * List Your Bookmarks View block
 * @author This addon creates a list of indiviual blocks,<br>with your bookmarks in it.
<ul><li>Export the Bookmarks from your browser(s)</li>
<li>Import your bookmarks into a list.</li>
<li>Add a small text and an image to each of the bookmarks.</li>
<li>Edit each bookmark like any normal block.</li>
<li>Each whole block is a link to another website.
 * @version 0.1
 * @package List Your Bookmarks View block
 */

defined('C5_EXECUTE') or die("Access Denied.");
$f = $controller->getFileObject();
$fp = new Permissions($f);
$blockType = BlockType::getByHandle('pc_shooter_ch_list_favorites');
$temp = $blockType->getBlockTypeCustomTemplates();
$templates['choose'] = t('Block Display');
foreach ($temp as $key => $value) {
    $templates[$value] = ucfirst(str_replace('_', ' ', $value));
}
//Loader::library('form_builder', 'pc_shooter_ch_list_favorites');

if ($fp->canViewFile()) {
    $c = Page::getCurrentPage();
    if ($c instanceof Page) {
        $cID = $c->getCollectionID();
    }
    ?>
<div id="block-title"><?php echo $btPcShooterChListFavoritesBlockText ?></div>
    <?php

    foreach ($bookMarkData as $bookmark) {
        ?>
        <div class="bookmark-row">
        <?php
        $i = 0;
        $isUrl = '';
        $text = '';
        $href = '';
        foreach($bookmark as $key => $value) {
            if (in_array($key, $fieldsToShow)){
                if (strpos($key, 'btPcShooterChListFavoritesBookMarksText') !== false){
                    $t = $value;
                }
                if (strpos($key, 'btPcShooterChListFavoritesBookMarksIcon') !== false){
                    $value = '<img src="' . $value . '" />';
                }
                if (strpos($value, 'title_') !== false) {
                    //$text = str_replace('title_', '', $value);
                }
                if (strpos($value, 'http') !== false) {
                    $text = '<a href="' . $value . '" target="' . $controller->btPcShooterChListFavoritesBlockLinksTarget . '">' . $t . '</a>';
                } else {
                    $text = $value;
                }
                //$url = ($isUrl) ? '<a href="' . $href . '" target="_blank">' . $text . '</a>' : $text;
                ?>
                <div class="row <?php echo 'row-num-' . strval($i) ?>"><?php echo $text ?></div>
                <?php

            }
            $i++;
        }
        ?>
        </div>
        <div style="clear: both;"></div>
        <?php
    }
    ?>
<?php
}

$fo = $this->controller->getFileObject();?>
