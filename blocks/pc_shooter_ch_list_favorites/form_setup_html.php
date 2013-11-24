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
$c = Page::getCurrentPage();
$c->getCollectionID();
$blockType = BlockType::getByHandle('pc_shooter_ch_list_favorites');
$temp = $blockType->getBlockTypeCustomTemplates();
foreach ($temp as $key => $value) {
    $templates[$value] = t(ucfirst(str_replace('_', ' ', $value)));
}
$cols = $controller->getBookmarkTableColumnsNames();
$columns['Counter'] = t('Counter');
foreach ($cols as $key => $value) {
    $columns[$value] = substr($value, 35, strlen($value));
}
$fieldsToShow = explode(',', $btPcShooterChListFavoritesBlockDisplayFields);

?>

    <script xmlns="http://www.w3.org/1999/html">
    var clear_html_extension_ajax = '<?php echo $clear_html_extension_ajax ?>',
        ajaxCall = '<?php echo $check_url; ?>',
        parse_html = '<?php echo $parse_html ?>',
        get_bookmarks = '<?php echo $get_bookmarks ?>',
        save_bookmarks = '<?php echo $save_bookmarks ?>',
        update_sort = '<?php echo $update_sort ?>',
        delete_bookmarks_by_id = '<?php echo $delete_bookmarks_by_id ?>',
        bookMarkData = '<?php echo $jData ?>',
        blockID = '<?php echo $controller->bID ?>',
        BlankImage = '<?php echo $blankImage;  ?>', // 26 Bytes
        delete_unused_bookmarks_from_db = '<?php echo $delete_unused_bookmarks_from_db; ?>',
        pkgHandle = '<?php echo $controller->getPkgHandle(); ?>',
        jData = null,
        folderImg = '<?php echo BASE_URL . DIR_REL . '/' . DIRNAME_PACKAGES . '/pc_shooter_ch_list_favorites/css/images/folder-small.png' ?>',
        jDt = null,
        BTStr_seeErrors = 'seeErrors_',
        BTStr_checkUrl = 'testbookmark_',
        bookmarkStartPos = 0,
        bookmarkStartClass = '',
        bookmarkStartID = 0,
        bookmarkEndPos = 0;
    $(document).ready(function () {
        var deleteAll = $('#DeleteAll');
        deleteAll.attr('disabled', true);
        $('#pc-shooter-ch-list-favorites-tabset a').click(function (ev) {
            var tab_to_show = $(this).attr('href');
            $('#pc-shooter-ch-list-favorites-tabset li').
                removeClass('ccm-nav-active').
                find('a').
                each(function (ix, elem) {
                    var tab_to_hide = $(elem).attr('href');
                    $(tab_to_hide).hide();
                });
            $(tab_to_show).show();
            $(this).parent('li').addClass('ccm-nav-active');
            return false;
        }).first().click();

        <?php
        if ($c->isEditMode()){
        ?>
        $('.hide').show();
        <?php
          }else {
          ?>
        $('.hide').hide();
        <?php
        }
        ?>
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
                    bookmarkStartClass = $(ui.item[0].children[1]).attr('class');
                    bookmarkStartID = $(ui.item[0]).attr('id');
                    bookmarkStartPos = $(ui.item[0].children[1]).html();
                },
                update: function (event, ui) {
                    bookmarkEndPos = $(ui.item[0].children[1]).html();
                    refreshPosition();
                }
            }
        );
        $('.table-condensed').disableSelection();

        $('#deleteAllIDs').live('click', function (){
            var checkBoxes = $('[id^="deleteID_"]');
            checkBoxes.each(function () {
                window.console.log(this);
                if ($(this).attr('checked')) {
                    $(this).attr('checked', false);
                    deleteAll.prop('disabled', true);
                } else {
                    $(this).attr('checked', true);
                    deleteAll.prop('disabled', false);
                }
            })
        })

        $('[id^="deleteID_"]').live('click', function() {
            if ($(this).attr('checked')) {
                deleteAll.prop('disabled', false);
            } else {
                deleteAll.prop('disabled', true);
            }
        })

        deleteAll.live('click', function(e) {
            var data = [],
                conf = confirm(ccm_t('sure'));
            e.preventDefault();
            if (conf) {
                $('[id^="deleteID_"]').each(function(i, n) {
                    if ($(this).attr('checked')) {
                        data.push({
                            bookmarkID: $(this).attr('id').split('_')[1]
                        });
                    }
                })
                console.log(data)
                deleteSelected(data);
                return false;
            }
        })
    })
</script>
<style>
    /*.file-choose-header {
        float: left;
        padding: 1em;
    }*/
</style>
    <div>
        <ul id="pc-shooter-ch-list-favorites-tabset" class="ccm-dialog-tabs" style="margin-left: -1px">
            <li><a href="#manage-bookmarks"><?php echo t("Manage bookmarks"); ?></a></li>
            <li><a href="#block-options"><?php echo t("Block options"); ?></a></li>
        </ul>
    </div>
    <div id="manage-bookmarks">
        <div>
            <div class="file-choose-header">
                <?php
                print $form->hidden('fileLinkText', 'bmf');
                print $al->file('ccm-b-file', 'fID', t('Choose HTM/HTML- File'), $this->fID, $htmlFilter);
                echo $this->fID;
                ?>
            </div>
            <div class="file-choose-header">
            <?php
                print $form->text('btPcShooterChListFavoritesBlockText', $btPcShooterChListFavoritesBlockText, array('placeholder' => t('Bookmarks Title')));
                ?>
            </div>
            <div class="file-choose-header">
                <button class="btn hide" id="DeleteAll">
                    <?php
                    print t('Delete selected');
                    ?>
                </button>
            </div>
        </div>
        <table class="table table-condensed">
            <thead>
                <tr class="hide">
                    <td colspan="9">
                        <?php print t('Entries') . ': <span id="numRecords"></span>'; ?>
                    </td>
                </tr>
                <tr class="hide">
                    <th colspan="2">
                        <input id="deleteAllIDs" type="checkbox" />
                        <?php print t('All'); ?>
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
                </tr>
            </thead>
            <tbody id="editBookmarks">
            </tbody>
        </table>
    </div>
    <div id="block-options">
        <div class="file-choose-header">
            <?php
            echo '<h4>' . t('Block') . '-' . t('View') . '</h4>';
            echo $form->select('btPcShooterChListFavoritesBlockDisplayBlock', $templates, $btPcShooterChListFavoritesBlockDisplayBlock);
            ?>
        </div>
        <div class="file-choose-header">
            <?php
            echo '<h4>' . t('Fields to show') . '</h4>';
            foreach ($columns as $key => $c) {
                echo '<label class="checkbox">';
                if (sizeof($btPcShooterChListFavoritesBlockDisplayFields) > 0) {
                    if (in_array($key, $fieldsToShow)) {
                        echo $form->checkbox('btPcShooterChListFavoritesBlockDisplayFields[]', $key, true) . ' ' . t($c);
                    } else{
                        echo $form->checkbox('btPcShooterChListFavoritesBlockDisplayFields[]', $key, false) . ' ' . t($c);
                    }
                } else {
                    echo $form->checkbox('btPcShooterChListFavoritesBlockDisplayFields[]', $key, true) . ' ' . t($c);
                }
                echo '</label>';
            }
            ?>
        </div>
        <div class="file-choose-header">
            <?php
            $target = array(
                '_blank' => t('Opens the linked document in a new window or tab'),
                '_self' => t('Opens the linked document in the same frame as it was clicked (this is default)'),
                '_parent' => t('Opens the linked document in the parent frame'),
                '_top' => t('Opens the linked document in the full body of the window')
            );
            echo '<h4>' . t('Link') . '-' . t('Target') . '</h4>';
            echo $form->select('btPcShooterChListFavoritesBlockLinksTarget', $target, $btPcShooterChListFavoritesBlockLinksTarget);
            echo '(' . $btPcShooterChListFavoritesBlockLinksTarget . ')';
            ?>
        </div>
    </div>

<?php