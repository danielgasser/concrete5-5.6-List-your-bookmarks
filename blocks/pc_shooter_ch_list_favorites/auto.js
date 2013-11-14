// Parent function instances
var selectedFile = ccm_alSelectFile;
var error = 0;

ccm_alSelectFile = function(args) {
    //lert(args);
    window.console.log(error);
    if (error > 10) return false;
    $.ajax({
        type: 'GET',
        async: false,
        url: upload_html,
        data: {
            fileID: args,
            pkg: pkgHandle
        },
        success: function (data) {
            var jData = $.parseJSON(data);
            window.console.log(jData);
            error += 1;
        }
    });
    error = 0;
    selectedFile(args);
}
