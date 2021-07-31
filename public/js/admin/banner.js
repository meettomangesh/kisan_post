siteObjJs.admin.bannerJs = function () {
    var maxImageSize = 2097152;
    // Initialize all the page-specific event listeners here.
    
    var initializeListener = function () {        
        $("body").on('change', '.image_name', function () {
            var billSelectError = '';
            var form = $(this).closest("form");
            var formId = form.attr("id");
            if (typeof (FileReader) != "undefined") {
                var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.png|.bmp)$/;
                if (regex.test(this.files[0].name.toLowerCase())) {
                    if (this.files[0].size > maxImageSize) {
                        billSelectError += this.files[0].name + ' is not selected. Maximum image size allowed is 2MB only.';
                        $('input[type="file"]').val(null);
                        $('#' + formId + " span#file-error-container").attr("style", "color: red").text(billSelectError).addClass('help-block-error');
                        setTimeout(function(){
                            $('#' + formId + " span#file-error-container").text("").removeClass('help-block-error');
                        }, 3000);
                        return false;
                    }
                } else {
                    $('input[type="file"]').val(null);
                    $('#' + formId + " span#file-error-container").attr("style", "color: red").text(this.files[0].name + " is not a valid image file.").addClass('help-block-error');
                    setTimeout(function(){
                        $('#' + formId + " span#file-error-container").text("").removeClass('help-block-error');
                    }, 3000);
                    return false;
                }
            } else {
                console.log("This browser does not support HTML5 FileReader.");
            }
        });

        $("body").on('click', '#remove-btn', function () {
            var r = confirm("Are you sure you want to delete this image?");
            if (r == true) {
                var rowId = $(this).attr('data-image-id');
                $("#blank-row-"+rowId).remove();
                if($("#dvPreview").children().length == 0) {
                    $(".banner-image-div").remove();
                    $("#image_name").removeAttr('disabled');
                }
            }
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            initializeListener();
        }

    };
}();