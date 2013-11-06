/**
 * Created with JetBrains PhpStorm.
 * User: temp
 * Date: 21.09.13
 * Time: 09:42
 */
$(document).ready(function(){
    "use strict";
    var formValue = {};

    $('#thafile').change(function(e){
        formValue = parseHtml(e.target.files);
    })


})

/**
 * Adds the chosen bookmark-file to a dom element (#showFile) for further parsing
 * @param ev: file change-event
 */
parseHtml = function (ev) {
    if (window.File && window.FileReader && window.FileList && window.Blob) {
        f = ev[0];
        if (f) {
            var r = new FileReader();
            r.onload = function (e) {
                contents = e.target.result;
                $('#showFile').html(contents);
                $('#showFile').hide();
                getLinks();
            };
            r.readAsText(f);
            $('#showFile').html('');
        }
    } else {
        //TODO error messages
    }
};

/**
 * Gets the dt>h3 and dt>a elements from ('#showFile')
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
                text: linkText,
                dateAdded: showDate,
                icon: linkIcon,
                url: jsLink,
                isLink: (linkRef.length > 0)
            }
        );
    });
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
        title,
        titleEnd,
        urlChange = null,
        oddEven,
        blankImg = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D'; // 26 Bytes

    //TODO in Version 2.0: add icons
    //var fstr = '<div class="formentry"><input type="file" id="btPcShooterChListFavoritesIcon" name="btPcShooterChListFavoritesIcon[]" value="' + l.icon + '" /></div>';
    for (i = 0; i < l.length; i += 1) {
        showImg = (l[i].icon.length === 0) ? blankImg : l[i].icon;
        title = (l[i].isLink) ? '' : '<h3>';
        titleEnd = (l[i].isLink) ? '' : '</h3>';
        oddEven = (i % 2 === 0) ? 'even' : 'odd';
        numrec = '';

        fstr += '<div class="links-form ' + oddEven + '">';
        if (i === 0) {
            numrec = ccm_t('num-records')  + ': ' + l.length;
        }
        fstr += '<div class="formentry">' + numrec + '</div>';
        fstr += '<div class="break"></div>';
        if (l[i].isLink) {
            fstr += '<div class="formentry"><img name="icon" id="icon" src="' + showImg + '" /></div>';
        }
        fstr += '<input type="hidden" name="btPcShooterChListFavoritesBookMarksIcon[]" id="btPcShooterChListFavoritesBookMarksIcon_' + i + '" value="' + showImg + '" />';
        fstr += '<div class="formentry">' + title + '<input class="ccm-input-text" type="text" id="btPcShooterChListFavoritesBookMarksText_' + i + '" name="btPcShooterChListFavoritesBookMarksText[]" value="' + l[i].text + '" />' + titleEnd + '</div>';
        if (l[i].isLink) {
            fstr += '<div class="formentry"><input type="text" id="btPcShooterChListFavoritesBookMarksDate_' + i + '" name="btPcShooterChListFavoritesBookMarksDate[]" value="' + l[i].dateAdded + '" /></div>';
            fstr += '<div class="formentry"><input type="text" id="btPcShooterChListFavoritesBookMarksUrl_' + i + '" name="btPcShooterChListFavoritesBookMarksUrl[]" value="' + l[i].url + '" /></div>';
            fstr += '<div class="formentry"><input type="button" name="testbookmark_' + i + '" class="testbookmark" id="' + ajaxCall + '_' + l[i].url + '" value="' + ccm_t('test-link') + '" /></div>';
            fstr += '<div id="showerrors" class="formentry"></div>';
        } else {
            fstr += '<div class="formentry"><input type="hidden" id="btPcShooterChListFavoritesBookMarksDate_' + i + '" name="btPcShooterChListFavoritesBookMarksDate[]" value="" /></div>';
            fstr += '<div class="formentry"><input type="hidden" id="btPcShooterChListFavoritesBookMarksUrl_' + i + '" name="btPcShooterChListFavoritesBookMarksUrl[]" value="" /></div>';
        }
        fstr += '</div>';
        fstr += '<div class="break"></div>';
        $('#editBookmarks').append(fstr);
        fstr = '';
        urlChange = document.getElementById('btPcShooterChListFavoritesBookMarksUrl_' + i);
        urlChange.addEventListener('change', function(){updateTestbookMarkValue($(this).val(), $(this).attr('id').split('_')[1]), false});
    }
    $('#editBookmarks').append('<input type="hidden" name="numRecords" id="numRecords" value="' + l.length + '">');
}

