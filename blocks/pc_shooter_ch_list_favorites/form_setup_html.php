<?php
defined('C5_EXECUTE') or die("Access Denied.");

Loader::helper('text');
$al = Loader::helper('concrete/asset_library');
$date = Loader::helper("date");
$html = Loader::helper('html');
$form = Loader::helper('form');
$ah = Loader::helper('concrete/interface');

$includeAssetLibrary = true;
if ($controller->getFileID() > 0) {
    $bf = $controller->getFileObject();
}
print_r($soUndSo);
?>

<ul id="tabset" class="ccm-dialog-tabs">
    <li> <a href="#managebookmarks"><?php echo t("Manage bookmarks"); ?></a></li>
    <li><a href="#blockoptions"><?php echo t("Block options"); ?></a></li>
</ul>
<script>
    var ajaxCall = '<?= $check_url; ?>',
        saveForm = '<?php echo $this->action("save_form"); ?>',
        urlChange = null,
        data = {};


    data = '<?php echo json_encode($soUndSo); ?>';
    $(document).ready(function () {
        data = $.parseJSON(data);
        window.console.log(data)
        if (data !== null){
            $.each(data, function (i, n) {
                delete data[i].blockID;
                delete data[i].bookmarkID;
            })
        }
    })
</script>

<div id="managebookmarks">
    <label for="thafile"><?php echo t('Select bookmark.htm(l)'); ?></label>
    <br>
    <input class="ccm-form-fileset" name="thafile" id="thafile" type="file" />
    <input class="s-file-text" name="btPcShooterChListFavoritesBlockText" id="btPcShooterChListFavoritesBlockText" type="text" value="<?php echo $controller->btPcShooterChListFavoritesBlockText ?>" />
    <div id="showFile">
    </div>
    <div id="editBookmarks">
        <script>
            if (data !== null) {
                createForm(data);
            }
        </script>
        <?php
 /*
            if (sizeof($soUndSo) > 0){
                $i = 0;
                $blankImg = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D'; // 26 Bytes
                while($i < sizeof($soUndSo)){
                    $showImg = (strlen($soUndSo[$i]['btPcShooterChListFavoritesBookMarksIcon']) == 0) ? $blankImg : $soUndSo[$i]['btPcShooterChListFavoritesBookMarksIcon'];
                    if ($soUndSo[$i]['btPcShooterChListFavoritesBookMarksUrl'] == '') {
                        $isLink = false;
                        $title = '<h3>';
                        $titleEnd = '</h3>';
                    } else {
                        $isLink = true;
                        $titleEnd = '';
                        $title = '';
                    }
                    $oddEven = ($i % 2 == 0) ? 'even' : 'odd';

                    echo '<div class="links-form ' . $oddEven . '">';
                    if ($i === 0) {
                        $numrec = t('num-records')  . ': ' . sizeof($soUndSo);
                    }
                    echo '<div class="formentry">' . $numrec . '</div>';
                    echo '<div class="break"></div>';
                    echo '<div class="formentry"><img name="icon" id="icon" src="' . $showImg . '" /></div>';
                    echo '<input type="hidden" name="btPcShooterChListFavoritesBookMarksIcon[]" id="btPcShooterChListFavoritesBookMarksIcon_' . $i . '" value="' . $showImg . '" />';
                    echo '<div class="formentry">' . $title;
                    echo '<input class="ccm-input-text" type="text" id="btPcShooterChListFavoritesBookMarksText_' . $i . '" name="btPcShooterChListFavoritesBookMarksText[]" value="' . $soUndSo[$i]['btPcShooterChListFavoritesBookMarksText'] . '" />' . $titleEnd . '</div>';

                    if ($isLink) {
                        echo '<div class="formentry"><input type="text" id="btPcShooterChListFavoritesBookMarksDate_' . $i . '" name="btPcShooterChListFavoritesBookMarksDate[]" value="' . $soUndSo[$i]['btPcShooterChListFavoritesBookMarksDate'] . '" /></div>';
                        echo '<div class="formentry"><input type="text" id="btPcShooterChListFavoritesBookMarksUrl_' . $i . '" name="btPcShooterChListFavoritesBookMarksUrl[]" value="' . $soUndSo[$i]['btPcShooterChListFavoritesBookMarksUrl'] . '" /></div>';
                        echo '<div class="formentry"><input type="button" name="testbookmark_' . $i . '" class="testbookmark" id="' . $ajaxCall . '_' . $soUndSo[$i]['btPcShooterChListFavoritesBookMarksUrl'] . '" value="' . t('Test bookmark.') . '" /></div>';
                    } else {
                        echo '<div class="formentry"><input type="hidden" id="btPcShooterChListFavoritesBookMarksDate_' . $i . '" name="btPcShooterChListFavoritesBookMarksDate[]" value="" /></div>';
                        echo '<div class="formentry"><input type="hidden" id="btPcShooterChListFavoritesBookMarksUrl_' . $i . '" name="btPcShooterChListFavoritesBookMarksUrl[]" value="" /></div>';
                    }
                    echo '</div>';
                    echo '<div class="break"></div>';
                    ?>
                    <script>
                        urlChange = document.getElementById('btPcShooterChListFavoritesBookMarksUrl_' + '<?php echo $i ?>');
                        urlChange.addEventListener('change', function(){updateTestbookMarkValue('<?php echo $soUndSo[$i]['btPcShooterChListFavoritesBookMarksUrl'] ?>', '<?php echo $i ?>'), false});
                    </script>
                    <?php
                    $i++;
                }
                 echo '<input type="hidden" name="numRecords" id="numRecords" value="' .sizeof($soUndSo) . '">';
            }
 */
        ?>
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
