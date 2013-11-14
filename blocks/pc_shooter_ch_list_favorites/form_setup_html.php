<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
$helper_file = Loader::helper('concrete/file');
$htmlFilter = array("fExtension" => "html");
?>
<script>
    var clear_html_extension_ajax = '<?= $clear_html_extension_ajax ?>',
        upload_html = '<?= $upload_html ?>',
        pkgHandle = '<?php echo $controller->getPkgHandle(); ?>';
</script>
    <pre>
    <?php
    print_r($bookMarkData);
    ?>
</pre>
<?php

/*
 * $u = new User();
$uID = $u->getUserID();
$fp = FilePermissions::getGlobal();
$fs = FileSet::createAndGetSet('bookmark', FileSet::TYPE_PRIVATE, $uID = false);
echo $fs->getFileSetID();
echo 'glo';
echo $r = $fs->overrideGlobalPermissions();
//$fs->resetPermissions();
$ui = UserInfo::getByID($uID);
$fp->setPermissions($ui, PTYPE_MINE, PTYPE_MINE, PTYPE_MINE, PTYPE_MINE, PTYPE_MINE, $extensions = array('html'));
echo 'glo after';
echo $r = $fs->overrideGlobalPermissions();

if (!$fp->canAddFiles()) {

    die('Unable to add files');

}
*/
//FileTypeList::define('html');
//if (!$fp->canAddFileType($cf->getExtension($pathToFile))) {
//
//   // return FileImporter::E_FILE_INVALID_EXTENSION;
//
//}

//echo $pkg->config('UPLOAD_FILE_EXTENSIONS_ALLOWED');


print $form->label('btPcShooterChListFavoritesBlockText', t('Bookmarks Title'));
print $form->text('btPcShooterChListFavoritesBlockText', $this->btPcShooterChListFavoritesBlockText);
print $form->hidden('fileLinkText', 'bmf');
print $al->file('ccm-b-file', 'fID', t('Choose HTM/HTML- File'), $this->fID, $htmlFilter);


echo $this->fID;
//echo $fh->getContents($f = $controller->getFileObject(), $timeout = 5);
