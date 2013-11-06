/**
 * Created with JetBrains PhpStorm.
 * User: temp
 * Date: 21.09.13
 * Time: 09:42
 */
var fieldIDs = [
    "btPcShooterChListFavoritesIcon",
    "btPcShooterChListFavoritesDate",
    "btPcShooterChListFavoritesUrl",
    "btPcShooterChListFavoritesText"
];
var oTable = $('#editBookmarksTable').dataTable({
        "bAutoWidth": true,
        "bProcessing": true,
        "aoColumnDefs": [
            {
                "mData": "icon",
                "sClass": "btPcShooterChListFavoritesIcon",
                "aTargets": [ 0 ]
            },
            {
                "mData": "dateAdded",
                "sClass": "btPcShooterChListFavoritesDate",
                "aTargets": [ 1 ]
            },
            {
                "mData": "text",
                "sClass": "btPcShooterChListFavoritesText",
                "aTargets": [ 2 ]
            },
            {
                "mData": "url",
                "sClass": "btPcShooterChListFavoritesUrl",
                "aTargets": [ 3 ]
            }
        ],
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $(nRow).attr("id", iDisplayIndexFull);
            $.each(fieldIDs, function () {
                $('.btPcShooterChListFavoritesIcon', nRow).html('<input type="file" id="btPcShooterChListFavoritesIcon" name="btPcShooterChListFavoritesIcon" value="' + aData.icon + '" />');
                $('.btPcShooterChListFavoritesDate', nRow).html('<input type="text" id="btPcShooterChListFavoritesDate" name="btPcShooterChListFavoritesDate" value="' + aData.dateAdded + '" />');
                $('.btPcShooterChListFavoritesText', nRow).html('<input type="text" id="btPcShooterChListFavoritesText" name="btPcShooterChListFavoritesText" value="' + aData.text + '" />');
                $('.btPcShooterChListFavoritesUrl', nRow).html('<input type="text" id="btPcShooterChListFavoritesUrl" name="btPcShooterChListFavoritesUrl" value="' + aData.url + '" />');
                //$('td', nRow).attr( 'id', this );
                //$('td', nRow).attr( 'name', this );
            })
            //window.console.log(aData);
            return nRow;
        }
    }
);

$(document).ready(function(){
    "use strict";
    var formValue = {};
    $('input:file').change(function(e){
        formValue = parseHtml(e.target.files);
        window.console.log(formValue);
    })
})
parseHtml = function (ev) {
    if (window.File && window.FileReader && window.FileList && window.Blob) {
        f = ev[0];
        if (f) {
            var r = new FileReader();
            r.onload = function (e) {
                contents = e.target.result;
                $('#showFile').html(contents);
                $('#showFile').hide();
                getLinks(contents);
            };
            r.readAsText(f);
        }
    } else {
        //TODO error messages
    }
};
getLinks = function (data) {
    var links = [],
        linkRef = null,
        jsLink = null,
        linkTimeStampAdded = null,
        linkDateAdded = null,
        showDate = null,
        linkIcon = null,
        linkText = null,
        oSettings = null,
        nTr = null,
        newRow = null;
    $.each($('dt>h3, dt>a'), function (index) {
        linkRef = ($(this).attr('href') === undefined) ? '' : $(this).attr('href');
        linkTimeStampAdded = ($(this).attr('add_date') === undefined) ? '' : $(this).attr('add_date');
        linkIcon = ($(this).attr('icon') === undefined) ? '' : '<img src="' + $(this).attr('icon') + '" />';
        linkText = ($(this).html() === undefined) ? '' : $(this).html();

        linkDateAdded = new Date(linkTimeStampAdded * 1000);
        showDate = linkDateAdded.getFullYear() + '-' + linkDateAdded.getMonth() + '-' + linkDateAdded.getDate();
        jsLink = (linkRef.indexOf('javascript') > -1) ? 'No JavaScript-links allowed!' : linkRef;
        links.push(
            {
                text: linkText,
                dateAdded: showDate,
                icon: linkIcon,
                url: jsLink
            }
        );
    });
    oTable.fnClearTable();
    oTable.fnAddData(links);
};
createForm = function (lnks) {
}