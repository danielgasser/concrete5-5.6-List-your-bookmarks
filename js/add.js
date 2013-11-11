/**
 * Created with JetBrains PhpStorm.
 * User: temp
 * Date: 21.09.13
 * Time: 09:42
 */
var jDt = {};


$(document).ready(function(){
    "use strict";

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

    $('[id^="' + BTStr_seeErrors + '"]').live('click', function () {
        var entryId = $(this).attr('id').split('_')[1],
            instance = $('[name="' + BTStr_checkUrl + '' + entryId + '"]'),
            data = instance.attr('id').split('__'),
            ref = $('#btPcShooterChListFavoritesBookMarksUrl_' + entryId).val();
        //jDt = checkBookMark(data, instance);
        loadUrlErrorDialog(jDt, ref);
    })
})

/**
 * Adds the chosen bookmark-file to a dom element (#showFile) for further parsing
 * REQUIRES FILE READER API
 * @param ev: file change-event
 */
parseHtml = function (ev) {
    if (window.File && window.FileReader && window.FileList && window.Blob) {
        var f = ev[0];
        if (f) {
            var r = new FileReader();
            r.onload = function (e) {
                contents = e.target.result;
                jQUSel_ShowFile.html(contents);
                jQUSel_ShowFile.hide();
                getLinks();
            };
            r.readAsText(f);
            jQUSel_ShowFile.html('');
        }
    } else {
        window.alert('ie9')
        //TODO error messages
    }
};

/**
 * Gets the dt>h3 and dt>a elements from ('#jQUSel_ShowFile')
 * into a javascript object
 */
getLinks = function () {
    var links = [],
        linkRef = null,
        jsLink = null,
        linkTimeStampAdded = null,
        linkDateAdded = null,
        showDate = null,
        linkIcon = null,
        linkText = null,
        formString = '<form>';
    $.each($('dt>h3, dt>a'), function (index) {
        linkRef = ($(this).attr('href') === undefined) ? '' : $(this).attr('href');
        linkTimeStampAdded = ($(this).attr('add_date') === undefined) ? '' : $(this).attr('add_date');
        linkIcon = ($(this).attr('icon') === undefined) ? '' : $(this).attr('icon');
        linkText = ($(this).html() === undefined) ? '' : $(this).html();

        linkDateAdded = new Date(linkTimeStampAdded * 1000);
        showDate = linkDateAdded.getFullYear() + '-' + linkDateAdded.getMonth() + '-' + linkDateAdded.getDate();
        jsLink = (linkRef.indexOf('javascript') > -1) ? ccm_t('no-js-links') : linkRef;
        links.push(
            {
                btPcShooterChListFavoritesBookMarksText: linkText,
                btPcShooterChListFavoritesBookMarksDate: showDate,
                btPcShooterChListFavoritesBookMarksIcon: linkIcon,
                btPcShooterChListFavoritesBookMarksUrl: jsLink
            }
        );
    });
    window.console.log(links)
    createForm(links);
};

/**
 * Constructs form fields for each entry from getLinks()
 * adds 'test bookmark' button
 * @param l: js-object
 * @returns {string} : the form
 */
createForm = function (l) {
    var fstr = '',
        i,
        numrec = '',
        showImg,
        titleFlag = 'title_',
        title = '',
        titleEnd = '',
        replaceTitleText = '',
        urlChange = null,
        checkUrl = null,
        displayCheck = null,
        printDisplayCheck = null,
        seeErrors = null,
        oddEven,
        isLink = true,
        blankImg = BlankImage; // 26 Bytes

    //TODO in Version 2.0: add icons
    //var fstr = '<div class="formentry"><input type="file" id="btPcShooterChListFavoritesIcon" name="btPcShooterChListFavoritesIcon[]" value="' + l.icon + '" /></div>';
    $('#editBookmarks').html('');

    fstr += '<div class="links-form meta-data">';
    fstr += '<div class="formentry">' + ccm_t('num-records') + ': ' + l.length + '</div>';
    fstr += '</div>';
    fstr += '<div class="break"></div>';
    fstr += '<div class="links-form meta-data">';
    fstr += '<div class="formentry formentry-icon"><!-- icon column --></div>';
    fstr += '<div class="formentry">' + ccm_t('meta-title') + '</div>';
    fstr += '<div class="formentry">' + ccm_t('meta-date') + '</div>';
    fstr += '<div class="formentry">' + ccm_t('meta-url') + '</div>';
    fstr += '<div class="formentry">' + ccm_t('meta-check') + '</div>';
    fstr += '<div class="formentry">' + ccm_t('meta-errors') + '</div>';
    fstr += '<div class="formentry">' + ccm_t('meta-delete') + '</div>';
    fstr += '</div>';
    fstr += '<div class="break"></div>';

    for (i = 0; i < l.length; i += 1) {

        showImg = (l[i].btPcShooterChListFavoritesBookMarksIcon.length === 0) ? blankImg : l[i].btPcShooterChListFavoritesBookMarksIcon;
        if (l[i].btPcShooterChListFavoritesBookMarksText.indexOf(titleFlag) > -1){
            replaceTitleText = l[i].btPcShooterChListFavoritesBookMarksText.substr(titleFlag.length, l[i].btPcShooterChListFavoritesBookMarksText.length);
            title = '<h3>';
            titleEnd = '</h3>';
            isLink = false;
        } else {
            replaceTitleText = l[i].btPcShooterChListFavoritesBookMarksText;
            isLink = true;
            title = '';
            titleEnd = '';
        }
        oddEven = (i % 2 === 0) ? 'even' : 'odd';

        fstr += '<div class="links-form ' + oddEven + '">';
        if (isLink) {
            fstr += '<div class="formentry formentry-icon"><img name="icon" id="icon" src="' + showImg + '" /></div>';
        }
        fstr += '<input type="hidden" name="btPcShooterChListFavoritesBookMarksIcon[]" id="btPcShooterChListFavoritesBookMarksIcon_' + i + '" value="' + showImg + '" />';
        fstr += '<div class="formentry">' + title + '<input class="ccm-input-text" type="text" id="btPcShooterChListFavoritesBookMarksText_' + i + '" name="btPcShooterChListFavoritesBookMarksText[]" value="' + replaceTitleText + '" />' + titleEnd + '</div>';
        if (isLink) {
            fstr += '<div class="formentry"><input type="text" id="btPcShooterChListFavoritesBookMarksDate_' + i + '" name="btPcShooterChListFavoritesBookMarksDate[]" value="' + l[i].btPcShooterChListFavoritesBookMarksDate + '" /></div>';
            fstr += '<div class="formentry"><input type="text" id="btPcShooterChListFavoritesBookMarksUrl_' + i + '" name="btPcShooterChListFavoritesBookMarksUrl[]" value="' + l[i].btPcShooterChListFavoritesBookMarksUrl + '" /></div>';
            fstr += '<div class="formentry"><input type="button" name="testbookmark_' + i + '" class="testbookmark" id="' + ajaxCall + '__' + l[i].btPcShooterChListFavoritesBookMarksUrl + '" value="' + ccm_t('test-link') + '" /></div>';
            fstr += '<div id="showerrors_' + i +'" class="formentry"></div>';
        } else {
            fstr += '<div class="formentry"><input type="hidden" id="btPcShooterChListFavoritesBookMarksDate_' + i + '" name="btPcShooterChListFavoritesBookMarksDate[]" value="" /></div>';
            fstr += '<div class="formentry"><input type="hidden" id="btPcShooterChListFavoritesBookMarksUrl_' + i + '" name="btPcShooterChListFavoritesBookMarksUrl[]" value="" /></div>';
        }
        fstr += '</div>';
        fstr += '<div class="break"></div>';
        jQUSel_EditBookMarks.append(fstr);
        fstr = '';

        //---- Event Listeners ----

        // Updating button with url value
        urlChange = document.getElementById('btPcShooterChListFavoritesBookMarksUrl_' + i);
        urlChange.addEventListener('change', function(){
            updateTestbookMarkValue($(this).val(), $(this).attr('id').split('_')[1])
                , false
        });

        // Test bookmark
        checkUrl = document.getElementById(ajaxCall + '__' + l[i].btPcShooterChListFavoritesBookMarksUrl);
        if(checkUrl !== null) {
            checkUrl.addEventListener('click', function(){
                window.event.preventDefault();
                var instance = this,
                    data = $(this).attr('id').split('__');
                window.console.log('?????? ' + data);
                jDt = checkBookMark(data, instance)
                    , false
            });
        }

        // See header details button
        seeErrors = document.getElementById('showerrors_' + i);
        if(seeErrors !== null) {
            seeErrors.addEventListener('click', function(){
                window.event.preventDefault();
                var entryId = $(this).attr('id').split('_')[1],
                    instance = $('[name="' + BTStr_checkUrl + '' + entryId + '"]'),
                    data = instance.attr('id').split('__'),
                    ref = $('#btPcShooterChListFavoritesBookMarksUrl_' + entryId).val();
                //jDt = checkBookMark(data, instance);
                loadUrlErrorDialog(jDt, ref)
                , false
            });
        }

        // See header details dialog
        displayCheck = document.getElementById(BTStr_seeErrors + '' + i);
        if (displayCheck !== null) {
            displayCheck.addEventListener('click', function () {
                window.event.preventDefault();
                var ref = $('#btPcShooterChListFavoritesBookMarksUrl_' + i).val();
                window.console.log('isches das' +ref)
                loadUrlErrorDialog(jDt, ref)
                    , false
            });
        }

        // Test bookmark
        displayCheck = document.getElementById(BTStr_seeErrors + '' + i);
        if (displayCheck !== null) {
            displayCheck.addEventListener('click', function () {
                window.event.preventDefault();
                var ref = $('#btPcShooterChListFavoritesBookMarksUrl_' + i).val();
                window.console.log(ref)
                loadUrlErrorDialog(jDt, ref)
                    , false
            });
        }
    }
    jQUSel_EditBookMarks.append('<input type="hidden" name="numRecords" id="numRecords" value="' + l.length + '">');
}

/**
 *
 * @param valData
 * @param instance
 * @returns {{}}
 */
checkBookMark = function (valData, instance) {
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
        errorButton = (c !== null) ? '<div class="formentry" style="float: right;" id="cE' + c + '"><input id="' + BTStr_seeErrors + '' + c + '" type="button" value="' + ccm_t('see-errors') + '" />' : '';

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

$('#url-error-dialog-print').live('click', function (e) {
    window.console.log('printDisplayCheck')
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
