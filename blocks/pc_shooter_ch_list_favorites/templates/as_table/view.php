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
echo $bID;
echo $btPcShooterChListFavoritesBlockText;
echo $btPcShooterChListFavoritesBlockMultiBlock;
$f = $controller->getFileObject();
$fp = new Permissions($f);

//Loader::library('form_builder', 'pc_shooter_ch_list_favorites');

if ($fp->canViewFile()) {
    $c = Page::getCurrentPage();
    if ($c instanceof Page) {
        $cID = $c->getCollectionID();
    }
    ?>
    <a href="<?php echo View::url('/download_file', $controller->getFileID(), $cID) ?>"><?php echo stripslashes($controller->getLinkText()) ?></a>
<pre>as table
    <?php
    print_r($bookMarkData);
    ?>
</pre>
<?php
}

$fo = $this->controller->getFileObject();?>
<a href="<?php echo $fo->getRelativePath()?>"><?php echo  stripslashes($controller->getLinkText()) ?></a>
