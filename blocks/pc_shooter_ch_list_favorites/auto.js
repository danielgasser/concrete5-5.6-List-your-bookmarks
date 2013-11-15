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
            //window.console.log(jData);
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
        isLink = true,
        blankImg = BlankImage,
        jQUSel_EditBookMarks = $('#editBookmarks'),
        updateTestbookMarkValue = 'testbookmark_';

     //TODO in Version 2.0: add icons
     //var fstr = '<div class="formentry"><input type="file" id="btPcShooterChListFavoritesIcon" name="btPcShooterChListFavoritesIcon[]" value="' + l.icon + '" /></div>';
    jQUSel_EditBookMarks.html('');

    window.console.log('------- createForm each --------');
    window.console.log(l);
    $.each(l, function(i, n){
        showImg = (n.btPcShooterChListFavoritesBookMarksIcon === null || n.btPcShooterChListFavoritesBookMarksIcon.indexOf('data:image') === -1) ? blankImg : n.btPcShooterChListFavoritesBookMarksIcon;
        if (n.btPcShooterChListFavoritesBookMarksText.indexOf(titleFlag) > -1) {
            replaceTitleText = n.btPcShooterChListFavoritesBookMarksText.substr(titleFlag.length, n.btPcShooterChListFavoritesBookMarksText.length);
            title = '<td colspan="10"><h3>';
            titleEnd = '</h3></td>';
            isLink = false;
        } else {
            replaceTitleText = n.btPcShooterChListFavoritesBookMarksText;
            isLink = true;
            title = '';
            titleEnd = '';
        }
        oddEven = (i % 2 === 0) ? 'even' : 'odd';

        // Construct form string
        fstr += '<tr>';
        fstr += '<td>' + (i + 1) + '</td>';
        if (isLink) {
            fstr += '<td><img name="icon" id="icon" src="' + showImg + '" /></td>';
        }
        fstr += '<input type="hidden" name="btPcShooterChListFavoritesBookMarksIcon[]" id="btPcShooterChListFavoritesBookMarksIcon_' + i + '" value="' + showImg + '" />';
        fstr += formEntryTDStart + title + '<input class="ccm-input-text" type="text" id="btPcShooterChListFavoritesBookMarksText_' + i + '" name="btPcShooterChListFavoritesBookMarksText[]" value="' + replaceTitleText + '" />' + titleEnd + '</td>';
        if (isLink) {
            fstr += formEntryTDStart + '<input type="text" id="btPcShooterChListFavoritesBookMarksDate_' + i + '" name="btPcShooterChListFavoritesBookMarksDate[]" value="' + l[i].btPcShooterChListFavoritesBookMarksDate + '" /></td>';
            fstr += formEntryTDStart + '<input type="text" id="btPcShooterChListFavoritesBookMarksUrl_' + i + '" name="btPcShooterChListFavoritesBookMarksUrl[]" value="' + l[i].btPcShooterChListFavoritesBookMarksUrl + '" /></td>';
            fstr += formEntryTDStart + '<input type="button" name="testbookmark_' + i + '" class="testbookmark" id="' + ajaxCall + '__' + l[i].btPcShooterChListFavoritesBookMarksUrl + '" value="' + ccm_t('test-link') + '" /></td>';
            fstr += '<div id="showerrors_' + i + '" class="formentry"></td>';
        } else {
            fstr += '<input type="hidden" id="btPcShooterChListFavoritesBookMarksDate_' + i + '" name="btPcShooterChListFavoritesBookMarksDate[]" value="" /></td>';
            fstr += '<input type="hidden" id="btPcShooterChListFavoritesBookMarksUrl_' + i + '" name="btPcShooterChListFavoritesBookMarksUrl[]" value="" /></td>';
        }
        fstr += '</tr>';
        jQUSel_EditBookMarks.append(fstr);
        fstr = '';
    })
    $('#ccm-dialog-loader-wrapper').hide();

    /*
         for (i = 0; i < l.length; i += 1) {


         fstr += '<div class="links-form ' + oddEven + '">';
         if (isLink) {
         fstr += '<div class="formentry formentry-icon"><img name="icon" id="icon" src="' + showImg + '" /></div>';
         }
         fstr += '<input type="hidden" name="btPcShooterChListFavoritesBookMarksIcon[]" id="btPcShooterChListFavoritesBookMarksIcon_' + i + '" value="' + showImg + '" />';
         fstr += formEntryDivStart + title + '<input class="ccm-input-text" type="text" id="btPcShooterChListFavoritesBookMarksText_' + i + '" name="btPcShooterChListFavoritesBookMarksText[]" value="' + replaceTitleText + '" />' + titleEnd + '</div>';
         if (isLink) {
         fstr += formEntryDivStart + '<input type="text" id="btPcShooterChListFavoritesBookMarksDate_' + i + '" name="btPcShooterChListFavoritesBookMarksDate[]" value="' + l[i].btPcShooterChListFavoritesBookMarksDate + '" /></div>';
         fstr += formEntryDivStart + '<input type="text" id="btPcShooterChListFavoritesBookMarksUrl_' + i + '" name="btPcShooterChListFavoritesBookMarksUrl[]" value="' + l[i].btPcShooterChListFavoritesBookMarksUrl + '" /></div>';
         fstr += formEntryDivStart + '<input type="button" name="testbookmark_' + i + '" class="testbookmark" id="' + ajaxCall + '__' + l[i].btPcShooterChListFavoritesBookMarksUrl + '" value="' + ccm_t('test-link') + '" /></div>';
         fstr += '<div id="showerrors_' + i + '" class="formentry"></div>';
         } else {
         fstr += formEntryDivStart + '<input type="hidden" id="btPcShooterChListFavoritesBookMarksDate_' + i + '" name="btPcShooterChListFavoritesBookMarksDate[]" value="" /></div>';
         fstr += formEntryDivStart + '<input type="hidden" id="btPcShooterChListFavoritesBookMarksUrl_' + i + '" name="btPcShooterChListFavoritesBookMarksUrl[]" value="" /></div>';
         }
         fstr += '</div>';
         fstr += '<div class="break"></div>';
         jQUSel_EditBookMarks.append(fstr);
         fstr = '';

     */
/*
     //---- Event Listeners ----

     // Updating button with url value
     urlChange = document.getElementById('btPcShooterChListFavoritesBookMarksUrl_' + i);
     urlChange.addEventListener('change', function () {
     updateTestbookMarkValue($(this).val(), $(this).attr('id').split('_')[1])
     , false
     });

     // Test bookmark
     checkUrl = document.getElementById(ajaxCall + '__' + l[i].btPcShooterChListFavoritesBookMarksUrl);
     if (checkUrl !== null) {
     checkUrl.addEventListener('click', function () {
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
     if (seeErrors !== null) {
     seeErrors.addEventListener('click', function () {
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
     window.console.log('isches das' + ref)
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
  *//*
     }
*/

     jQUSel_EditBookMarks.append('<input type="hidden" name="numRecords" id="numRecords" value="' + l.length + '">');

}
