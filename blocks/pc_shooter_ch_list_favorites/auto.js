
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
        var ref = $(this).attr('id').split('_')[1],
            instance = $(this).parent('div'),
            go = $('[name="testbookmark_' + ref + '"]').attr('id').split('_');
        jDt = checkBookMark(go, instance, false);
        loadUrlErrorDialog(jDt);
    })
    $('[name^="testbookmark_').live('click', function (e) {
        e.preventDefault();
        var instance= this,
            data = $(this).attr('id').split('_');
        jDt = checkBookMark(data, instance, true);
    })
})
checkBookMark = function (valData, instance, detailsClick){
    var goto,
        d,
        ref = valData[valData.length - 1],
        jData = {};
    d = valData.splice(valData.length - 1, 1)
    goto = valData.join('_');

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
            var text = '',
                isNull = (data === 'false') ? true : false;
            jData = $.parseJSON(data);
            window.console.log(jData);
            $.each(jData, function(i, n){
                window.console.log(i);
                $('#cE' + i).remove();
                if (isNull) {
                    $(instance).val(ccm_t('bookmark-error'));
                    text = ccm_t('no-errors-to-show');
                } else if ($.isNumeric(i)){
                    if (n.indexOf('200') > -1){
                        $(instance).val(ccm_t('bookmark-valid'));
                    } else {
                        $(instance).val(ccm_t('bookmark-error'));
                        $(instance).parent('div').append('<div class="formentry" style="float: right;" id="cE' + i + '"><input id="seeErrors_' + i + '" type="button" value="' + ccm_t('see-errors') + '" />');
                        return false;
                    }
                }
            });
            //$(instance).parent('div').append(text + '</div>');
            $('#ajax-loader').hide();
        }
    });
    return jData;
}
loadUrlErrorDialog = function(obj) {
    var str = '<div id="url-error-dialog">',
        falseStr = '';
    $.each(obj, function(n, i){
        window.console.log(n);
        window.console.log(i);
        falseStr = (i === false) ? ccm_t('no-errors-to-show') : i;
        str += '<div class="url-error-dialog-container"><span class="key">' + n + '</span>: <span class="value">\"' + falseStr + '\"</span></div>';
    })
    str += '</div>';
    window.console.log(obj);
    jQuery.fn.dialog.open({
        title: ccm_t('url-error-dialog-title') + ': ' + obj.TestedUrl,
        element: str,
        width: 550,
        modal: false,
        height: 275,
        onClose: function() {
            //alert('This will fire when the dialog is closed.');
        }
    });
}
updateTestbookMarkValue = function (el, index) {
    $('[name="testbookmark_' + index + '"]').attr('id', ajaxCall + '_' + el);
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