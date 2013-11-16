<?php
/**
 * List Your Bookmarks Add/Edit form
 * @author This addon creates a list of indiviual blocks,<br>with your bookmarks in it.
<ul><li>Export the Bookmarks from your browser(s)</li>
<li>Import your bookmarks into a list.</li>
<li>Add a small text and an image to each of the bookmarks.</li>
<li>Edit each bookmark like any normal block.</li>
<li>Each whole block is a link to another website.
 * @version 0.1
 * @package List Your Bookmarks Add/Edit form
 */

defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$includeAssetLibrary = true;
$al = Loader::helper('concrete/asset_library');
$helper_file = Loader::helper('concrete/file');
$htmlFilter = array("fExtension" => "html");
$jData = $this->action('get_bookmark_data_json');
?>
    <script>
    var clear_html_extension_ajax = '<?php echo $clear_html_extension_ajax ?>',
        ajaxCall = '<?= $check_url; ?>',
        parse_html = '<?php echo $parse_html ?>',
        get_bookmarks = '<?php echo $get_bookmarks ?>',
        bookMarkData = '<?php echo $jData ?>',
        blockID = '<?php echo $controller->bID ?>',
        BlankImage = '<?php echo $blankImage;  ?>', // 26 Bytes
        delete_unused_bookmarks_from_db = '<?php echo $delete_unused_bookmarks_from_db; ?>',
        pkgHandle = '<?php echo $controller->getPkgHandle(); ?>',
        jData = null;
    $(document).ready(function () {
        window.console.log('waaaaaaaas' + blockID.length);
        if (blockID.length > 0) {
            $('#ccm-dialog-loader-wrapper').show();
            $.ajax({
                type: 'GET',
                url: get_bookmarks,
                data: {
                    blockID: blockID
                },
                success: function (data) {
                    jData = $.parseJSON(data);
                    window.console.log('this');
                    window.console.log(jData);
                    createForm(jData);
                }
            });
        }
    })

</script>
<div class="ccm-ui">
    <table class="table table-condensed">
        <thead>
            <tr>
                <td colspan="10">
                    <?php

                    print $form->label('btPcShooterChListFavoritesBlockText', t('Bookmarks Title'));
                    print $form->text('btPcShooterChListFavoritesBlockText', $this->btPcShooterChListFavoritesBlockText);
                    print $form->hidden('fileLinkText', 'bmf');
                    print $al->file('ccm-b-file', 'fID', t('Choose HTM/HTML- File'), $this->fID, $htmlFilter);
                    ?>
                    </td>
            </tr>

            <tr>
                <td colspan="10">
                    <?php print t('Entries') . ': ' . sizeof($bookMarkData); ?>
                </td>
            </tr>
            <tr>
                <th>
                    #
                </th>
                <th>
                    <?php // Icon; ?>
                </th>
                <th>
                    <?php print t('Title'); ?>
                </th>
                <th>
                    <?php print t('Date added'); ?>
                </th>
                <th>
                    <?php print t('www'); ?>
                </th>
                <th>
                    <?php print t('Test bookmark'); ?>
                </th>
                <th>
                    <?php print t('See header details'); ?>
                </th>
                <th>
                    <?php print t('Delete'); ?>
                </th>
                <th>
                    <?php print t('Move'); ?>
                </th>
            </tr>
        </thead>
        <tbody id="editBookmarks">
        </tbody>
    </table>
</div>
<?php