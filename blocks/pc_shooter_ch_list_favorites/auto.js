
$(document).ready(function () {

    var jDt = {};


    $('#tabset a').click(function (ev) {
        var tab_to_show = $(this).attr('href');
        $('#tabset li').
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


    $('[id^="seeErrors_"]').live('click', function () {
        var entryId = $(this).attr('id').split('_')[1],
            instance = $('[name="testbookmark_' + entryId + '"]'),
            data = instance.attr('id').split('__'),
            ref = $('#btPcShooterChListFavoritesBookMarksUrl_' + entryId).val();
        jDt = checkBookMark(data, instance);
        loadUrlErrorDialog(jDt, ref);
    })

    $('[name^="testbookmark_"]').live('click', function (e) {
        e.preventDefault();
        var instance= this,
            data = $(this).attr('id').split('__');
        jDt = checkBookMark(data, instance);
    })

    $('#url-error-dialog-print').live('click', function (e) {
        //e.preventDefault();
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
    window.console.log('---------------');
    window.console.log(jDt);
})

/**
 *
 * @param valData
 * @param instance
 * @returns {{}}
 */
checkBookMark = function (valData, instance){
    var goto,
        d,
        ref = valData[valData.length - 1],
        jData = {};
    d = valData.splice(valData.length - 1, 1)
    goto = valData.join('_');
    window.console.log('goto')
    window.console.log(goto)
    window.console.log('ref')
    window.console.log(ref)
    $.ajax({
        type: 'POST',
        async: false,
        url: goto,
        data: {
            bookMark : ref
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
            window.console.log(data)
            var errorText = '',
                isNull = (data === 'false') ? true : false,
                urlValid = false,
                counter = null;
            jData = $.parseJSON(data);
            $.each(jData, function(i, n){
                if (isNull) {
                    urlValid = null;
                    //return false;
                } else if ($.isNumeric(i)){
                    errorText = n;
                    if (n.indexOf('200') > -1){
                        urlValid = true;
                    } else {
                        urlValid = false;
                        counter = i;
                    }
                }
            });
            // Display result of url-check
            displayUrlCheck(instance, urlValid);
            $('#ajax-loader').hide();
        }
    });
    return jData;
}


/**
 * Displays 'See error button' and/or header-status text
 * @param check: true, false or null
 */
displayUrlCheck = function (instance, check) {
    var c = $(instance).attr('name').split('_')[1],
        errorEl = (c !== null) ? $('#showerrors_' + c) : $(''),
        errorButton = (c !== null) ? '<div class="formentry" style="float: right;" id="cE' + c + '"><input id="seeErrors_' + c + '" type="button" value="' + ccm_t('see-errors') + '" />' : '';

    switch (check) {
        case null:
            $(instance).val(ccm_t('bookmark-error'));
            errorEl.html(errorButton);
            break;
        case true:
            $(instance).val(ccm_t('bookmark-valid'));
            break;
        case false:
            $(instance).val(ccm_t('bookmark-error'));
            errorEl.html(errorButton);
            break;
    }
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
    }else {
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
    $('[name="testbookmark_' + index + '"]').attr('id', ajaxCall + '__' + el);
}


/**
 *  ------ C5 Overrides -----------------
 */

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

ccmValidateBlockForm = function() {
    var fileArr = $('#thafile').val().split('.'),
        fileExt = fileArr[fileArr.length - 1],
        countErrors = 0;
    if (!/\b(htm|html)\b/i.test(fileExt)) {
        ccm_addError(ccm_t('no-html'));
        countErrors += 1;
    }
    //TODO in Version 2.0: add icons
    /**
     *     if (!/\b(jpg|jpeg|gif|png|ico)\b/i.test(imgExt) && imgArr.length > 0) {
        window.console.log(fileExt);
        ccm_addError(ccm_t('no-image') + imgExt);
        countErrors += 1;
    }

     */


    return (countErrors === 0);
}