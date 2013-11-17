/**
 * List Your Bookmarks auto.js
 * @author This addon creates a list of indiviual blocks,<br>with your bookmarks in it.
 <ul><li>Export the Bookmarks from your browser(s)</li>
 <li>Import your bookmarks into a list.</li>
 <li>Add a small text and an image to each of the bookmarks.</li>
 <li>Edit each bookmark like any normal block.</li>
 <li>Each whole block is a link to another website.
 * @version 0.1
 * @package List Your Bookmarks auto.js
 */

/**
 * Parent function overrides
 * @param args
 */
window.ccm_alSelectFile = function(args) {
    var jData = null;
    $('#ccm-dialog-loader-wrapper').show();
    $.ajax({
        type: 'GET',
        url: parse_html,
        data: {
            fileID: args
        },
        success: function (data) {
            jData = $.parseJSON(data);
            if (data === null) {
                ccm_addError(ccm_t('parsing-failed'));
                return false;
            }
            createForm(jData);
        }
    });
    jQuery.fn.dialog.closeTop();
}

/**
 * Parent function overrides
 *
 */
window.ccm_blockWindowClose = function() {
    $.ajax({
        type: 'GET',
        url: delete_unused_bookmarks_from_db
    });
    jQuery.fn.dialog.closeTop();
}


createForm = function (l) {
    var fstr = '',
        formEntryTDStart = '<td>',
        showImg,
        titleFlag = 'title_',
        title = '',
        titleEnd = '',
        replaceTitleText = '',
        oddEven,
        f,
        isLink = true,
        blankImg = BlankImage,
        jQUSel_EditBookMarks = $('#editBookmarks'),
        updateTestbookMarkValue = 'testbookmark_';

     //TODO in Version 2.0: add icons
     //var fstr = '<div class="formentry"><input type="file" id="btPcShooterChListFavoritesIcon" name="btPcShooterChListFavoritesIcon[]" value="' + l.icon + '" /></div>';
    jQUSel_EditBookMarks.html('');

    $.each(l, function(i, n){
        showImg = (n.btPcShooterChListFavoritesBookMarksIcon === null || n.btPcShooterChListFavoritesBookMarksIcon.indexOf('data:image') === -1) ? blankImg : n.btPcShooterChListFavoritesBookMarksIcon;
        if (n.btPcShooterChListFavoritesBookMarksText.indexOf(titleFlag) > -1) {
            replaceTitleText = n.btPcShooterChListFavoritesBookMarksText.substr(titleFlag.length, n.btPcShooterChListFavoritesBookMarksText.length);
            title = '<h3>';
            titleEnd = '</h3>';
            isLink = false;
            f = ' ' + titleFlag;
        } else {
            replaceTitleText = n.btPcShooterChListFavoritesBookMarksText;
            isLink = true;
            title = '';
            titleEnd = '';
            f = '';
        }
        oddEven = (i % 2 === 0) ? 'even' : 'odd';

        // Construct form string
        fstr += '<tr id="bookMarkID_' + n.bookmarkID + '">';
        fstr += formEntryTDStart + + (i + 1) + '</td>';
        fstr += formEntryTDStart + '<img name="icon" id="icon" src="' + showImg + '" /><input type="hidden" name="btPcShooterChListFavoritesBookMarksIcon[]" id="btPcShooterChListFavoritesBookMarksIcon_' + i + '" value="' + showImg + '" /></td>';
        fstr += formEntryTDStart + title + '<input class="ccm-input-text' + f + '" type="text" id="btPcShooterChListFavoritesBookMarksText_' + i + '" name="btPcShooterChListFavoritesBookMarksText[]" value="' + replaceTitleText + '" />' + titleEnd + '</td>';
        if (isLink) {
            fstr += formEntryTDStart + '<input class="datepicker_' + i + ' input-small" type="text" id="btPcShooterChListFavoritesBookMarksDate_' + i + '" name="btPcShooterChListFavoritesBookMarksDate[]" value="' + n.btPcShooterChListFavoritesBookMarksDate + '" /></td>';
            fstr += formEntryTDStart + '<input class="span4" type="text" id="btPcShooterChListFavoritesBookMarksUrl_' + i + '" name="btPcShooterChListFavoritesBookMarksUrl[]" value="' + n.btPcShooterChListFavoritesBookMarksUrl + '" />';
            fstr += formEntryTDStart + '<button name="testbookmark_' + i + '" class="testbookmark btn" id="' + ajaxCall + '__' + n.btPcShooterChListFavoritesBookMarksUrl + '">' + ccm_t('test-link') + '</button></td>';
            fstr += formEntryTDStart + '<input type="hidden" id="Zsort_' + i + '" name="Zsort[]" value="' + i + '" /></td>';
        } else {
            fstr += formEntryTDStart + '<input type="hidden" id="btPcShooterChListFavoritesBookMarksDate_' + i + '" name="btPcShooterChListFavoritesBookMarksDate[]" value="" /></td>';
            fstr += formEntryTDStart + '<input type="hidden" id="btPcShooterChListFavoritesBookMarksUrl_' + i + '" name="btPcShooterChListFavoritesBookMarksUrl[]" value="" /></td>';
            fstr += formEntryTDStart + '</td>';
            fstr += formEntryTDStart + '<input type="hidden" id="Zsort_' + i + '" name="Zsort[]" value="' + i + '" /></td>';
        }
        fstr += '</tr>';
        jQUSel_EditBookMarks.append(fstr);
        fstr = '';
        $('#btPcShooterChListFavoritesBookMarksDate_' + i).datepicker();
        $('#btPcShooterChListFavoritesBookMarksDate_' + i).datepicker(
            "setDate", n.btPcShooterChListFavoritesBookMarksDate, "option", "dateFormat", 'yy-mm-dd'
        );
    })
    $('#ccm-dialog-loader-wrapper').hide();

     jQUSel_EditBookMarks.append('<input type="hidden" name="numRecords" id="numRecords" value="' + l.length + '">');

}

checkBookMark = function (valData, instance) {
    var goto,
        d,
        ref = valData[valData.length - 1],
        jData = {};
    d = valData.splice(valData.length - 1, 1)
    goto = valData.join('_');
    $('#ccm-dialog-loader-wrapper').show();
    $.ajax({
        type: 'POST',
        async: false,
        url: goto,
        data: {
            bookMark: ref
        },
        /**
         * Returns jData: Object {
             *      0: "HTTP/1.0 301 Moved Permanently"
             *      1: "HTTP/1.0 302 Found"
             *      2: "HTTP/1.0 200 OK"
             *      ...
             * }
         * object is string false (php, sorry) on error
         *
         * @param data jSon-encoded PHP Array
         * @returns {boolean} used to break the for-loop
         *
         */

        success: function (data) {
            var errorText = '',
                isNull = (data === 'false') ? true : false,
                urlValid = false,
                counter = null;
            jData = $.parseJSON(data);
            $.each(jData, function (i, n) {
                goto = valData.join('i');
                window.console.log(i);
                if (isNull) {
                    urlValid = null;
                    //return false;
                } else if ($.isNumeric(i)) {
                    errorText = n;
                    if (n.indexOf('200') > -1) {
                        urlValid = true;
                    } else {
                        urlValid = false;
                        counter = i;
                    }
                }
            });
            // Display result of url-check
            displayUrlCheck(instance, urlValid);
            $('#ccm-dialog-loader-wrapper').hide();

        }
    });
    return jData;
}

saveBookmarksByID = function (id, args) {
    window.console.log(args);
    $.ajax({
        type: 'POST',
        url: save_bookmarks,
        data: {
            bookmarkID: id,
            fieldValues: args
        }
        //success: function (data) {
        //    jData = $.parseJSON(data);
        //    if (data === null) {
        //        ccm_addError(ccm_t('parsing-failed'));
        //        return false;
        //    }
        //    createForm(jData);
        //}
    });

}
/**
 * Displays 'See error button' and/or header-status text
 * @param check: true, false or null
 */

displayUrlCheck = function (instance, check) {
    console.log(instance);
    var valEl = (instance !== null) ? $('[name="testbookmark_' + instance + '"]') : $(''),
        errorEl = (instance !== null) ? valEl.parent().next('td') : $(''),
        errorButton = (instance !== null) ? '<button class="btn" id="' + BTStr_seeErrors + '' + instance + '">' + ccm_t('see-errors') + '</button>' : '';

    switch (check) {
        case null:
            valEl.html(ccm_t('bookmark-error'));
            errorEl.html(errorButton);
            break;
        case true:
            valEl.html(ccm_t('bookmark-valid'));
            break;
        case false:
            valEl.html(ccm_t('bookmark-error'));
            errorEl.html(errorButton);
            break;
    }
}

loadUrlErrorDialog = function (obj, ref) {
    var shortref = ref.substring(0, 47) + '... ';
    jQuery.fn.dialog.open({
        title: ccm_t('url-error-dialog-title') + ': ' + '<div class="url-error-dialog-ref">' + shortref + '</div>',
        element: createDialogElement(obj),
        width: 550,
        modal: false,
        height: 275
    });
}

/**
 *
 * @param data
 * @returns {string}
 */

createDialogElement = function (data) {
    var str = '<div id="url-error-dialog">',
        falseStr = '';
    if (falseStr = (typeof data === 'boolean')) {
        falseStr = ccm_t('no-errors-to-show');
        str += '<div class="url-error-dialog-container"><span class="value">' + falseStr + '</span></div>';
    } else {
        $.each(data, function (n, i) {
            str += '<div class="url-error-dialog-container"><span class="key">' + n + '</span>: <span class="value">\"' + falseStr + i + '\"</span></div>';
        })
    }
    str += '<div class="ccm-buttons dialog-buttons">' +
        '<a href="javascript::void(0)" id="url-error-dialog-close" class="btn ccm-button-right cancel">' + ccm_t('close') + '</a>' +
        '<a href="javascript::void(0)" id="url-error-dialog-print" class="btn ccm-button-left cancel">' + ccm_t('print') + '</a>' +
        '</div>';
    //jQuery.fn.dialog
    str += '</div>';
    return str += '<iframe id="url-error-dialog-print-content" style="height: 0px; width: 0px; position: absolute"></iframe>'
}


/**
 *
 * @param el
 * @param index
 */

updateTestbookMarkValue = function (el, index) {
    $('[name="' + BTStr_checkUrl + '' + index + '"]').attr('id', ajaxCall + '__' + el);
}
