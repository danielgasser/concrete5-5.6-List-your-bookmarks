
$(document).ready(function () {

    var jDt = {};

   // $('#tabset').tabs();
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

    $('#thafile').change(function (e) {
        window.console.log('$.parseJSON(data)');
        window.console.log(e.target.files[0]);
        var jData = [];
        $.ajax({
            type: 'GET',
            async: false,
            url: uploadHtml,
            data: {
                thaFile: e.target.files[0].value,
                thaFileName: e.target.files[0].name
            },
            success: function (data) {
                jData = $.parseJSON(data);
                window.console.log(jData);
                createForm(jData['data']);
            }
        });
        // FILE API REQUIRED
        // formValue = parseHtml(e.target.files);
    })


    $('[id^="' + BTStr_seeErrors + '"]').live('click', function () {
        var entryId = $(this).attr('id').split('_')[1],
            instance = $('[name="' + BTStr_checkUrl + '' + entryId + '"]'),
            data = instance.attr('id').split('__'),
            ref = $('#btPcShooterChListFavoritesBookMarksUrl_' + entryId).val();
        //jDt = checkBookMark(data, instance);
        loadUrlErrorDialog(jDt, ref);
    })

/*

    $('[name^="' + BTStr_checkUrl + '"]').click(function (e) {
        window.console.log('??????');
        e.preventDefault();
        var instance= this,
            data = $(this).attr('id').split('__');
        jDt = checkBookMark(data, instance);
    })


*/
    window.console.log('---------------');
    window.console.log(jDt);
})



/**
 *  ------ C5 Overrides -----------------
 */

ccmValidateBlockForm = function() {
    window.console.log('?');
    var fileArr = jQUSel_ThaFile.val().split('.'),
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


    return false;
}
