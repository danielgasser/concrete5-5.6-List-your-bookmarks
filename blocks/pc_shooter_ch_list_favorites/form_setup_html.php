<?php
defined('C5_EXECUTE') or die("Access Denied.");

Loader::helper('text');
$al = Loader::helper('concrete/asset_library');
$date = Loader::helper("date");
$html = Loader::helper('html');
$form = Loader::helper('form');
$ah = Loader::helper('concrete/interface');

$includeAssetLibrary = true;
?>
<ul id="tabset" class="ccm-dialog-tabs">
    <li> <a href="#managebookmarks"><?php echo t("Manage bookmarks"); ?></a></li>
    <li><a href="#blockoptions"><?php echo t("Block options"); ?></a></li>
</ul>
<script>
    var data = [];
    data = <?= json_encode($bookMarkData) ?>;
</script>
<script>
    var ajaxCall = '<?= $check_url; ?>',
        uploadHtml = '<?= $upload_html; ?>',
        saveForm = '<?php echo $this->action("save_form"); ?>',
        urlChange = null;
    $(document).ready(function () {
        //data = $.parseJSON(data);
        if (data !== null){
            $.each(data, function (i, n) {
                delete data[i].blockID;
                delete data[i].bookmarkID;
            })
        }
    })

// ---------------- jQuery Selectors, ID-Strings -----------------
    var jQUSel_ShowFile = $('#showFile'),
        JQUSel_ThaFile = $('#thafile'),
        jQUSel_EditBookMarks = $('#editBookmarks'),
        BTStr_seeErrors = 'seeErrors_',
        BTStr_checkUrl = 'testbookmark_',
        BlankImage = '<?php echo $blankImage;  ?>'; // 26 Bytes

</script>

<div id="managebookmarks">
    <form></form>
    <form action="<?= $upload_html ?>" enctype="multipart/form-data" method="POST">
        <?php
        echo $form->label('thafile', t('Select bookmark.htm(l)'));
        echo $form->file('thafile', 'thafile', t('Select bookmark.htm(l)'));
        ?>
        <!--label for="thafile"><?php echo t('Select bookmark.htm(l)'); ?></label-->
        <!--input name="thafile" id="thafile" type="file"/-->
        <!--input type="submit" value="Send File"/-->
    </form>
    <div id="chooseFile">
        <br>


        <!--input class="ccm-form-fileset" name="thafile" id="thafile" type="file" /-->
    </div>
    <label for="btPcShooterChListFavoritesBlockText"><?php echo t('Header text'); ?></label>
    <input class="s-file-text" name="btPcShooterChListFavoritesBlockText" id="btPcShooterChListFavoritesBlockText" type="text" value="<?php echo $controller->btPcShooterChListFavoritesBlockText ?>" />
    <div id="showFile">
    </div>
    <div id="editBookmarks">
        <script>
            if (data !== null) {
                createForm(data);
            }
        </script>
    </div>
</div>
<div id="blockoptions">
    <?php
   echo $form->label('btPcShooterChListFavoritesBlockMultiBlock', t('Block options'));
    echo '<br>';
    echo  $form->select('btPcShooterChListFavoritesBlockMultiBlock', array(
        'multi' => t('Each bookmark in a block'),
        'one' => t('One block for all bookmarks')), 'multi');
    ?>
</div>
