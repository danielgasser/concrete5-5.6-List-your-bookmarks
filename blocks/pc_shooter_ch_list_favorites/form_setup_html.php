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
        save_bookmarks = '<?php echo $save_bookmarks ?>',
        update_sort = '<?php echo $update_sort ?>',
        bookMarkData = '<?php echo $jData ?>',
        blockID = '<?php echo $controller->bID ?>',
        BlankImage = '<?php echo $blankImage;  ?>', // 26 Bytes
        delete_unused_bookmarks_from_db = '<?php echo $delete_unused_bookmarks_from_db; ?>',
        pkgHandle = '<?php echo $controller->getPkgHandle(); ?>',
        jData = null,
        jDt = null,
        BTStr_seeErrors = 'seeErrors_',
        BTStr_checkUrl = 'testbookmark_',
        bookmarkStartPos = 0,
        bookmarkStartClass = '',
        bookmarkStartID = 0,
        bookmarkEndPos = 0;
    $(document).ready(function () {
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
                    createForm(jData);
                }
            });
        }

        $('[name^="' + BTStr_checkUrl + '"]').live('click', function (e) {
            e.preventDefault();
            var instance = $(this).attr('name').split('_')[1],
                data = $(this).attr('id').split('__');
            jDt = checkBookMark(data, instance);
        })

        $('[id^="' + BTStr_seeErrors + '"]').live('click', function () {
            var entryId = $(this).attr('id').split('_')[1],
                instance = $('[name="' + BTStr_checkUrl + '' + entryId + '"]'),
                data = instance.attr('id').split('__'),
                ref = $('#btPcShooterChListFavoritesBookMarksUrl_' + entryId).val();
            //jDt = checkBookMark(data, instance);
            loadUrlErrorDialog(jDt, ref);
        })

        $('[id^="btPcShooterChListFavoritesBookMarksUrl_"]').live('change', function() {
            updateTestbookMarkValue($(this).val(), $(this).attr('id').split('_')[1]);
        })

        $('#url-error-dialog-print').live('click', function (e) {
            e.preventDefault();
            var content = document.getElementById("url-error-dialog");
            var pri = document.getElementById("url-error-dialog-print-content").contentWindow;
            pri.document.open();
            pri.document.write(content.innerHTML);
            pri.document.close();
            pri.focus();
            pri.print();
        })

        $('#url-error-dialog-close').live('click', function (e) {
            e.preventDefault();
            ccm_blockWindowClose();
        })
        $('input[id^="btPcShooterChListFavoritesBookMark"]').live('change', function(){
            var record = $(this).closest('tr'),
                data = [],
                bookmarkID = record.attr('id').split('_')[1];
            record.find('td > :hidden, :text').each(function(i, n){
                if ($(n).hasClass('title_')) {
                    data.push('title_' + $(n).val());
                } else {
                    data.push($(n).val());
                }
            })
            saveBookmarksByID(bookmarkID, data);
        })

        $('.table-condensed').sortable(
            {
                items: '.sortable_row',
                handle: '.sort_handle',
                start: function(event, ui) {
                    bookmarkStartClass = $(ui.item[0].children[0]).attr('class');
                    bookmarkStartID = $(ui.item[0]).attr('id');
                    bookmarkStartPos = $(ui.item[0].children[0]).html();
                },
                update: function (event, ui) {
                    bookmarkEndPos = $(ui.item[0].children[0]).html();
                    refreshPosition();
                }
            }
        );
        $('.table-condensed').disableSelection();


    })
refreshPosition = function(){
    var ids = [],
        j,
        id,
        oldid;
    $.each($('.' + bookmarkStartClass), function (i, n) {
        j = i + 1;
        $(this).text(j);
        //$('[id="Zsort_' + j + '"]').val(j);
    });
    $.each($('.inputZsort'), function (i, n) {
        j = i + 1;
        id = $(this).parent().parent().attr('id').split('_')[1];
        oldid = parseInt($(this).attr('id').split('_')[1], 10);
        $(this).val(i);
        window.console.log(oldid);
        ids.push(
        {
            bid: id,
            vale: i
        });
    });
    $.ajax({
        type: 'POST',
        url: update_sort,
        data: {
            sortage: ids
        },
        success: function (data) {
            jData = $.parseJSON(data);
            window.console.log(jData);
        }
    });
    window.console.log(ids)
}

</script>
<div>
    <table class="table table-condensed" border="1">
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
                    <?php print t('Entries') . ': <span id="numRecords"></span>'; ?>
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
                    <?php print t('Added'); ?>
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
                    <?php print t('Sort'); ?>
                </th>
                <th>
                    <?php print t('Delete'); ?>
                </th>
            </tr>
        </thead>
        <tbody id="editBookmarks">
        </tbody>
    </table>
</div>
<?php