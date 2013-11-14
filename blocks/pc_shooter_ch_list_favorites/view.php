<?php defined('C5_EXECUTE') or die("Access Denied.");
echo $bID;
echo $btPcShooterChListFavoritesBlockText;
echo $btPcShooterChListFavoritesBlockMultiBlock;
$f = $controller->getFileObject();
$fp = new Permissions($f);
$bookMarkData = $controller->getBookmarkData();

if ($fp->canViewFile()) {
    $c = Page::getCurrentPage();
    if ($c instanceof Page) {
        $cID = $c->getCollectionID();
    }
    ?>
    <a href="<?php echo View::url('/download_file', $controller->getFileID(), $cID) ?>"><?php echo stripslashes($controller->getLinkText()) ?></a>
<pre>
    <?php
    print_r($bookMarkData);
    ?>
</pre>
<?php
}

$fo = $this->controller->getFileObject();?>
<a href="<?php echo $fo->getRelativePath()?>"><?php echo  stripslashes($controller->getLinkText()) ?></a>
