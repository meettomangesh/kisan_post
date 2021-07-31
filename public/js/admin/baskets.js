siteObjJs.admin.basketMerchantJs = function () {
    var storedFiles = [];
    var storedNewFiles = [];
    var removedFileNames = [];
    var maxImageSize = 2097152;
    var maxImagesSelect = 5;
    var imageNamesArr = [];
    var rowNumber;
    var removedImagesIds = [];
    // Initialize all the page-specific event listeners here.

    var initializeListener = function (formId) {

        var validator = $('#' + formId).validate();
        validator.resetForm();
        //Reset everything when clicked on cancel
        $('body').on("click", ".remove-image", function () {
            var formElement = $(this.closest('form'));
            formElement.find('#product_image')[0].files[0] = "";
            $('.fileinput-new').find('.img-thumbnail').attr('src', defaultAttractionImg);
            formElement.find('#remove').val('remove');
        });

        $('body').on("click", ".change-image", function () {
            var formElement = $(this.closest('form'));
            formElement.find('#remove').val('');
        });

        $('body').on("click", ".btn-expand-form", function () {
            storedFiles = [];
            storedNewFiles = [];
        });
        $('body').on("click", ".portlet-title", function () {
            storedFiles = [];
            storedNewFiles = [];
        });

        $('#basket_image').bind('change', function (e) {
            //this.files[0].size gets the size of your file.
            if (this.files[0]) {
                if (this.files[0].size > 2097152) {
                    var error = siteObjJs.admin.validateUserJs.maxFileSize;
                    $('#file-error').text(error);
                    return false;
                }

                var ext = $('#basket_image').val().split('.').pop().toLowerCase();
                if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                    var error = siteObjJs.admin.validateUserJs.mimes;
                    $('#file-error').text(error);
                    return false;
                } else {
                    $('#file-error').text('');
                }

            }

        });

        //if "remove" button is clicked from input, clear error messages
        $("a.fileinput-exists").on('click', function () {
            $('#file-error').text('');
        });

        $('#selling_price').bind('change', function (e) {
            $('#selling_price_points_value').val($(this).val());
        });

        $('#special_price').bind('change', function (e) {
            $('#special_price_points_value').val($(this).val());
        });

        $('body').on("click", ".btn-collapse", function () {
            $("#ajax-response-text").html("");
            //retrieve id of form element and create new instance of validator to clear the error messages if any
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");

            var dvPreview = $("#" + formId).find("#dvPreview");
            dvPreview.html("");
            var blankRowDiv = $("<tr class='row-for-blank'>");
            var colDivRemove = $("<td colspan='6' class='text-center'>");
            colDivRemove.text("No image selected.");
            blankRowDiv.append(colDivRemove);
            dvPreview.append(blankRowDiv);

            storedFiles = [];
            storedNewFiles = [];
            removedFileNames = [];
            imageNamesArr = [];

            $("#" + formId + " #merchant_id").select2("val", "");
            $("#" + formId + " #product_id").select2("val", "");
            $("#" + formId + " #brand_id").select2("val", "");
            $("#" + formId + " #location_id_1")
                .empty()
                .append('<option value="">Select Locations</option>');
            $('#' + formId).find('#add-merchant-location').attr('disabled', true);
            $('#' + formId).find(".remove-location-id-div").remove();
            $('#' + formId).find('input[name="voucher_value"]').prop('readonly', true);

            var validator = $('#' + formId).validate();
            validator.resetForm();
            //remove any success or error classes on any form, to reset the label and helper colors
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');
        });

        /* $('#create-product').on('submit', function() {
            // return $('#testForm').jqxValidator('validate');
            var formElement = $(this);// Retrive form from DOM and convert it to jquery object
            var formData = formElement.serializeArray();
            var formId = formElement.attr("id");
            var form = new FormData();
            if (storedFiles.length > 0) {
                $.each(storedFiles, function (key, value) {
                    form.append('images[]', value);
                });
                $('#' + formId + " span#file-error-container").text("").removeClass('help-block-error');
            } else {
                $('#' + formId + " span#file-error-container").text("Please select at least one image.").addClass('help-block-error');
                return false;
            }
        }); */

        $('#edit-basket').on('submit', function () {
            if ($("#dvPreview").children().length == 0 && $('input[type="file"]').val() == '') {
                $("#edit-product span#file-error-container").attr("style", "color: red").text("Please select at least one image.").addClass('help-block-error red');
                setTimeout(function () {
                    $("#edit-product span#file-error-container").text("").removeClass('help-block-error');
                }, 3000);
                return false;
            }
        });

        $("body").on('click', '#remove-btn', function () {
            var r = confirm("Are you sure you want to delete this image?");
            if (r == true) {
                var rowId = $(this).attr('data-image-id');
                $("#blank-row-" + rowId).remove();
                removedImagesIds.push(rowId);
                $("#removed_images").val(removedImagesIds);
                if ($("#dvPreview").children().length == 0) {
                    $(".basket-image-div").remove();
                    $("#basket_image_name").removeAttr('disabled');
                }
            }
        });

        imageNamesArr = [];

        $("body").on('change', '.basket_images', function () {
            var billSelectError = '';
            var form = $(this).closest("form");
            var formId = form.attr("id");
            if (typeof (FileReader) != "undefined") {
                var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.png|.bmp)$/;
                var i = 0;
                $($(this)[0].files).each(function () {
                    // To select same image in chrome browser
                    var file = $(this);
                    if (regex.test(file[0].name.toLowerCase())) {
                        // Select bill image whose name is not exists for current bill.
                        // Select bill image whose size is less than 2MB.
                        // Select maximum 5 bill images.
                        if ($.inArray(file[0].name, imageNamesArr) == -1 && file[0].size < maxImageSize && imageNamesArr.length < maxImagesSelect) {
                            imageNamesArr.push(file[0].name);
                        } else {
                            if ($.inArray(file[0].name, imageNamesArr) >= 0 && !$.isEmptyObject(imageNamesArr)) {
                                billSelectError += file[0].name + ' is already exists.';
                            }
                            if (file[0].size > maxImageSize) {
                                billSelectError += file[0].name + ' is not selected. Maximum image size allowed is 2MB only.';
                            }
                            if (imageNamesArr.length >= maxImagesSelect) {
                                billSelectError += 'Maximum ' + maxImagesSelect + ' images allowed.';
                            }
                            $('input[type="file"]').val(null);
                            $('#' + formId + " span#file-error-container").attr("style", "color: red").text(billSelectError).addClass('help-block-error');
                            setTimeout(function () {
                                $('#' + formId + " span#file-error-container").text("").removeClass('help-block-error');
                            }, 3000);
                            return false;
                        }
                    } else {
                        $('input[type="file"]').val(null);
                        $('#' + formId + " span#file-error-container").attr("style", "color: red").text(file[0].name + " is not a valid image file.").addClass('help-block-error');
                        setTimeout(function () {
                            $('#' + formId + " span#file-error-container").text("").removeClass('help-block-error');
                        }, 3000);
                        return false;
                    }
                    i++;
                });
            } else {
                console.log("This browser does not support HTML5 FileReader.");
            }
        });

        $("body").on('change', '.fileupload', function () {
            var billSelectError = '';
            var form = $(this).parents("form");
            var dvPreview = form.find("#dvPreview");
            var form = $(this).closest("form");
            var formId = form.attr("id");
            if (typeof (FileReader) != "undefined") {
                var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.png|.bmp)$/;
                var i = 0;
                $($(this)[0].files).each(function () {
                    // To select same image in chrome browser
                    $('input[type="file"]').val(null);
                    var file = $(this);

                    if (regex.test(file[0].name.toLowerCase())) {
                        // Select bill image whose name is not exists for current bill.
                        // Select bill image whose size is less than 2MB.
                        // Select maximum 5 bill images.
                        if ($.inArray(file[0].name, imageNamesArr) == -1 && file[0].size < maxImageSize && imageNamesArr.length < maxImagesSelect) {
                            // Maintain array of current bill imagas name
                            imageNamesArr.push(file[0].name);
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                var rowDiv = $("<tr class='" + formId + "-" + i + "'>");
                                //var colDivNumber = $("<td>");
                                var colDivPreview = $("<td width='20%' style='vertical-align: middle; text-align: center;'>");
                                var colDivFileName = $("<td width='20%'>");
                                var colDivDescription = $("<td width='27%'>");
                                var colDivOrder = $("<td width='15%'>");
                                var colDivRemove = $("<td width='15%'>");

                                var img = $("<img />");
                                img.attr("style", "width:100%;");
                                img.attr("src", e.target.result);
                                colDivPreview.append(img);

                                var removeButton = $("<span class='btn red remove-row'>");
                                removeButton.text("Remove");
                                colDivRemove.append(removeButton);
                                colDivFileName.append(file[0].name);
                                rowNumber = imageNamesArr.length;
                                var inputDesc = $("<textarea class='form-control' name='image_desc[]' maxlength = '200'/>");
                                colDivDescription.append(inputDesc);

                                var inputOrder = $("<input type='text' class='form-control' name='display_order[]' maxlength='1' readonly='true' />");
                                colDivOrder.append(inputOrder);

                                rowDiv.append('<td>' + i + '</td>');
                                rowDiv.append(colDivPreview);
                                rowDiv.append(colDivFileName);
                                rowDiv.append(colDivDescription);
                                rowDiv.append(colDivOrder);
                                rowDiv.append(colDivRemove);

                                dvPreview.append(rowDiv);
                            }
                            reader.onloadend = function (e) {

                                storedFiles.push(e.target.result);
                                storedNewFiles.push(e.target.result);
                                if (storedFiles.length > 0) {
                                    $('tr.row-for-blank').hide();
                                    $('#' + formId + " span#file-error-container").text('');
                                } else {
                                    $('tr.row-for-blank').show();
                                }
                                var j;
                                /* if(dvPreview.find("tr:first").hasClass("row-for-blank")) {
                                 j = dvPreview.find("tr").length - 1;
                                 } else {
                                 j = dvPreview.find("tr").length;
                                 }*/
                                if (formId == 'edit-basket-merchant') {
                                    //var jEdit = j - 1;
                                    if (dvPreview.find("tr:first").hasClass("row-for-blank")) { //all the images removed and starting with blank row
                                        j = dvPreview.find("tr").length - 1;
                                        dvPreview.find("tr").eq(j).children('td').eq(0).text(j);
                                        dvPreview.find("tr").eq(j).children('td').eq(3).find('textarea').attr('name', 'image_desc[' + j + ']');
                                        dvPreview.find("tr").eq(j).children('td').eq(4).find('input[type="text"]').attr('name', 'display_order[' + j + ']');
                                        dvPreview.find("tr").eq(j).children('td').eq(4).find('input[type="text"]').attr('value', j);
                                    } else {
                                        j = dvPreview.find("tr").length;
                                        dvPreview.find("tr").eq(j - 1).children('td').eq(0).text(j);
                                        dvPreview.find("tr").eq(j - 1).children('td').eq(3).find('textarea').attr('name', 'image_desc[' + j + ']');
                                        dvPreview.find("tr").eq(j - 1).children('td').eq(4).find('input[type="text"]').attr('name', 'display_order[' + j + ']');
                                        dvPreview.find("tr").eq(j - 1).children('td').eq(4).find('input[type="text"]').attr('value', j);
                                    }
                                    return false;
                                } else {
                                    /*j = dvPreview.find("tr").length; //chk condition                                    
                                     var jMinus = j - 1;
                                     dvPreview.find("tr").eq(j - 1).children('td').eq(0).text(jMinus);
                                     dvPreview.find("tr").eq(j - 1).children('td').eq(3).find('textarea').attr('name', 'image_desc[' + jMinus + ']');
                                     dvPreview.find("tr").eq(j - 1).children('td').eq(4).find('input[type="text"]').attr('name', 'display_order[' + jMinus + ']');
                                     dvPreview.find("tr").eq(j - 1).children('td').eq(4).find('input[type="text"]').val(jMinus);*/

                                    if (dvPreview.find("tr:first").hasClass("row-for-blank")) { //all the images removed and starting with blank row
                                        j = dvPreview.find("tr").length - 1;
                                        dvPreview.find("tr").eq(j).children('td').eq(0).text(j);
                                        dvPreview.find("tr").eq(j).children('td').eq(3).find('textarea').attr('name', 'image_desc[' + j + ']');
                                        dvPreview.find("tr").eq(j).children('td').eq(4).find('input[type="text"]').attr('name', 'display_order[' + j + ']');
                                        dvPreview.find("tr").eq(j).children('td').eq(4).find('input[type="text"]').attr('value', j);
                                    } else {
                                        j = dvPreview.find("tr").length;
                                        dvPreview.find("tr").eq(j - 1).children('td').eq(0).text(j);
                                        dvPreview.find("tr").eq(j - 1).children('td').eq(3).find('textarea').attr('name', 'image_desc[' + j + ']');
                                        dvPreview.find("tr").eq(j - 1).children('td').eq(4).find('input[type="text"]').attr('name', 'display_order[' + j + ']');
                                        dvPreview.find("tr").eq(j - 1).children('td').eq(4).find('input[type="text"]').attr('value', j);
                                    }
                                }
                            }
                            reader.readAsDataURL(file[0]);
                        } else {
                            if ($.inArray(file[0].name, imageNamesArr) >= 0 && !$.isEmptyObject(imageNamesArr)) {
                                billSelectError += file[0].name + ' is already exists.<br>';
                            }
                            if (file[0].size > maxImageSize) {
                                billSelectError += file[0].name + ' is not selected. Maximum image size allowed is 2MB only.<br>';
                            }
                            if (dvPreview.find("tr").length == maxImagesSelect) {
                                billSelectError += file[0].name + ' is not selected. Maximum ' + maxImagesSelect + ' images allowed.<br>';
                            }
                        }
                    } else {
                        $('#' + formId + " span#file-error-container").text(file[0].name + " is not a valid image file.").addClass('help-block-error');
                        return false;
                    }
                    i++;
                });
                if (billSelectError != '') {
                    Metronic.alert({
                        type: 'danger',
                        message: billSelectError,
                        container: $('#ajax-response-text'),
                        place: 'prepend',
                        closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                    });
                }
            } else {
                console.log("This browser does not support HTML5 FileReader.");
            }
        });
        $("body").on('click', '.remove-row', function () {
            var form = $(this).parents('form');
            var formId = form.attr("id");
            var row = $(this).closest("tr");
            var dvPreview = $(this).parents('#dvPreview');
            if (formId == "edit-product-merchant") {
                //e.preventDefault();

                /*bootbox.confirm({
                 buttons: {confirm: {label: 'CONFIRM'}},
                 message: 'Are you sure you want to delete this image?',
                 callback: function (result) {
                 if (result === false) {
                 console.log(3);
                 return;
                 } else {
                 console.log(4);
                 proceedWithRemove(formId);
                 }
                 }
                 });*/
                proceedWithRemove(formId);
            } else {
                proceedWithRemove(formId);
            }
            function proceedWithRemove(formId) {
                //Find and remove the file data from storedFiles array               
                var index = $.inArray(row.find("img").attr("src"), storedFiles);
                if (index > -1) {
                    storedFiles.splice(index, 1);
                    storedNewFiles.splice(index, 1);
                } else {
                    var index2 = $.inArray(row.children().eq(2).text(), storedFiles);
                    if (index2 > -1) {
                        storedFiles.splice(index2, 1);
                    }
                }
                var filename = row.find('td:nth-child(3)').text();
                if (formId == 'edit-product-merchant') {
                    var filePath = row.find('input.s3-image-path').val();
                    removedFileNames.push(filePath + '/' + filename);
                } else {
                    removedFileNames.push(filename);
                }

                imageNamesArr = $.grep(imageNamesArr, function (value) {
                    return value != filename;
                });
                //delete the tr DOM
                row.remove();
                var j = 0;
                var k = 0;
                $.each(dvPreview.find("tr"), function () {
                    /*if (formId == 'edit-product-merchant') {
                     k = j;
                     } else {
                     k = j + 1;
                     }
                     var j1;
                     if (formId == 'edit-product-merchant') {
                     if (j == 0) {
                     j1 = j + 1;
                     } else {
                     j1 = j;
                     }
                     } else {
                     j1 = j + 1;
                     }
                     dvPreview.find("tr").eq(k).children('td').eq(0).text(j1);
                     dvPreview.find("tr").eq(k).children('td').eq(3).find('textarea').attr('name', 'image_desc[' + j1 + ']');
                     dvPreview.find("tr").eq(k).children('td').eq(4).find('input[type="text"]').attr('name', 'display_order[' + j1 + ']');
                     dvPreview.find("tr").eq(k).children('td').eq(4).find('input[type="text"]').val(j1);
                     j++;*/
                    var index;
                    if (formId == 'edit-product-merchant') {
                        k = j;
                        index = k + 1;
                    } else {
                        k = j + 1;
                        index = k;
                    }

                    dvPreview.find("tr").eq(k).children('td').eq(0).text(index);
                    dvPreview.find("tr").eq(k).children('td').eq(3).find('textarea').attr('name', 'image_desc[' + index + ']');
                    dvPreview.find("tr").eq(k).children('td').eq(4).find('input[type="text"]').attr('name', 'display_order[' + index + ']');
                    dvPreview.find("tr").eq(k).children('td').eq(4).find('input[type="hidden"]').attr('name', 'image_old_path[' + index + ']');
                    dvPreview.find("tr").eq(k).children('td').eq(4).find('input[type="text"]').attr('value', index);
                    //dvPreview.find('input[name=display_order[' + index + ']]').val(index);
                    j++;
                });
                //check if all the rows are deleted, and if yes add the default one
                if ($('.remove-row').length == 0) {
                    if (formId == 'edit-product-merchant') {
                        var htmlstring = '<tr id="blank-row" class="row-for-blank" style="display: table-row;"><td class="text-center" colspan="6">No image selected.</td></tr>';
                        dvPreview.html(htmlstring);
                    }
                    $('tr.row-for-blank').show();
                } else {
                    $('tr.row-for-blank').hide();
                }
            }
        });

        /*$('body').on("click", ".product_pic", function () {
         var imagePath = $(this).find("img").attr('src');
         $('.product-pic').attr('src', imagePath);
         $("#product-image-modal").modal({show: 'fade'});
         });*/

        /*$(function () {
         function position() {
         $(".positionable").position({
         //of: $("#parent"),
         my: "center center",
         at: "center center",
         collision: "fit fit"
         });
         }
         position();
         });*/


    };
    var handleCustomTextEvents = function (formId) {
        if ($("#" + formId + " input[name='display_custom_text_or_date']:checked").val() != 2) {
            $("#" + formId + " #custom_text_span").hide();
            $("#" + formId + " #custom_text").attr('disabled', 'disabled');
        }

        $("#" + formId + " input[name='display_custom_text_or_date']").on("change", function () {
            if ($(this).val() == 2) {
                $("#" + formId + " #custom_text_span").show();
                $("#" + formId + " #custom_text").removeAttr('disabled');
            } else {
                $("#" + formId + " #custom_text_span").hide();
                $("#" + formId + " #custom_text").attr('disabled', 'disabled');
            }
        });
    }
    // Method to fetch and place edit form with data using ajax call

    var fetchDataForEdit = function () {
        $('.portlet-body').on('click', '.edit-form-link', function () {
            var cat_id = $(this).attr("id");
            var actionUrl = 'products-merchant/' + cat_id + '/edit';
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                success: function (data) {
                    $("#edit_form").html(data.form);
                    storedFiles = [];
                    storedNewFiles = [];
                    $('#edit-product-merchant .select2me').select2();
                    $('form').find('input:radio').uniform();
                    handleBootstrapMaxlength();
                    handleDatePicker();
                    showLocationQtyOnEditForm(cat_id);
                    changeSpecialPrice('edit-product-merchant');
                    if (data.is_voucher_value == 1) {
                        $('#edit-product-merchant').find('input[name="voucher_value"]').prop('readonly', false);
                    }

                    $('#edit-product-merchant').find('#selling_price').bind('change', function (e) {
                        $('#edit-product-merchant').find('#selling_price_points_value').val($(this).val());
                    });

                    $('#edit-product-merchant').find('#special_price').bind('change', function (e) {
                        $('#edit-product-merchant').find('#special_price_points_value').val($(this).val());
                    });

                    var dvPreview = $("#edit-product-merchant").find("#dvPreview");
                    dvPreview.html("");

                    var htmlstring = '';
                    var j = 0;
                    var imageName;

                    if (data.images.length == 0) {
                        var htmlstring = '<tr id="blank-row" class="row-for-blank" style="display: table-row;"><td class="text-center" colspan="6">No image selected.</td></tr>';
                    }

                    $.each(data.images, function (key, image) {
                        var key1 = key + 1;
                        var image1 = image.split('***');
                        image = image1[0];
                        var imagePath = image1[1];
                        var imgDesc = image1[2];
                        var imgOrder = image1[3];
                        var rowDiv = $("<tr class='edit-product-merchant-1'>");
                        var colDivNumber = $("<td width='3%'>");
                        var colDivPreview = $("<td width='20%' style='vertical-align: middle; text-align: center;'>");
                        var colDivFileName = $("<td width='20%'>");
                        var colDivDescription = $("<td width='27%'>");
                        var colDivOrder = $("<td width='15%'>");
                        var colDivRemove = $("<td width='15%'>");

                        var img = '<div class="product_pic"><img style="height:height:100%;width:100%;" src="' + image + '"></div>'
                        colDivPreview.append(img);
                        colDivNumber.text(j + 1);
                        storedFiles.push(image);
                        var removeButton = '<span class="btn red remove-row">Remove</span>';
                        var imageNamesTemp = imagePath.split("/");

                        var imageName = imageNamesTemp[imageNamesTemp.length - 1];
                        colDivFileName.append(imageName);

                        var imageDesc = imgDesc;
                        var inputDesc = $("<textarea class='form-control' name='image_desc[" + key1 + "]' id='image_desc_" + key1 + "' maxlength = '200' />");
                        inputDesc.text(imageDesc);
                        colDivDescription.append(inputDesc);

                        imageNamesArr.push(imageName);
                        imageNamesTemp = $.grep(imageNamesTemp, function (value) {
                            return value != imageName;
                        });

                        if (imgOrder == 0) {
                            imgOrder = '';
                        }
                        var inputOrder = $("<input type='text' class='form-control' name='display_order[" + key1 + "]' id='display_order_" + key1 + "' maxlength='1' readonly='true' />");
                        inputOrder.attr('value', imgOrder);
                        var inputIdHidden = $("<input type='hidden' class='form-control' name='image_old_path[" + key1 + "]' />");
                        inputIdHidden.attr('value', imagePath);
                        colDivOrder.append(inputOrder);
                        colDivOrder.append(inputIdHidden);

                        var inputS3ImagePath = imageNamesTemp.join('/');
                        var inputS3ImagePathHidden = "<input type='hidden' class='form-control s3-image-path' name='s3-image-path' value='" + inputS3ImagePath + "'/>";
                        colDivRemove.append(removeButton + inputS3ImagePathHidden);
                        rowDiv.append(colDivNumber);
                        rowDiv.append(colDivPreview);
                        rowDiv.append(colDivFileName);
                        rowDiv.append(colDivDescription);
                        rowDiv.append(colDivOrder);
                        rowDiv.append(colDivRemove);


                        htmlstring = htmlstring + rowDiv.prop('outerHTML');
                        j++;
                    });

                    dvPreview.append(htmlstring);

                    siteObjJs.validation.formValidateInit('#edit-product-merchant', handleAjaxRequest);
                    handleCustomTextEvents('edit_form');
                },
                error: function (jqXhr, json, errorThrown) {
                    var errors = jqXhr.responseJSON;
                    var errorsHtml = '';
                    $.each(errors, function (key, value) {
                        errorsHtml += value[0] + '<br />';
                    });
                    // alert(errorsHtml, "Error " + jqXhr.status + ': ' + errorThrown);
                    Metronic.alert({
                        type: 'danger',
                        message: errorsHtml,
                        container: $('#ajax-response-text'),
                        place: 'prepend',
                        closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                    });
                }
            });
        });
    };
    // Common method to handle add and edit ajax request and reponse

    var handleAjaxRequest = function () {

        var formElement = $(this.currentForm);// Retrive form from DOM and convert it to jquery object
        var icon = 'check';
        var messageType = 'success';
        var formData = formElement.serializeArray();
        var actionUrl = formElement.attr('action');
        var actionType = formElement.attr('method');
        var formId = formElement.attr("id");

        var form = new FormData();

        if (formId == "edit-product-merchant") {
            form.append("edit_form", "true");
            if (removedFileNames.length > 0) {
                $.each(removedFileNames, function (key, value) {
                    form.append('removedImage[]', value);
                });
                $('#' + formId + " span#file-error-container").text("").removeClass('help-block-error');
            }
        }

        formData.reduce(function (obj, item) {
            form.append(item.name, item.value);
        });


        if (storedFiles.length > 0) {
            //$('#'+formId).find('image[]').empty();
            $.each(storedFiles, function (key, value) {
                form.append('image[]', value);
            });
            $('#' + formId + " span#file-error-container").text("").removeClass('help-block-error');
        } else {
            $('#' + formId + " span#file-error-container").text("Please select at least one image.").addClass('help-block-error');
            return false;
        }


        if (storedNewFiles.length > 0) {
            $.each(storedNewFiles, function (key, value) {
                form.append('image_new[]', value);
            });
        }
        form.append('_token', $('meta[name="csrf-token"]').attr('content'));

        if (formId == "edit-product-merchant") {
            form.append('_method', 'PUT');
        } else {
            form.append('_method', 'POST');
        }
        //$('.bill-image-loader').fadeIn(200);
        //$('html, body').css("cursor", "wait");

        $.ajax(
            {
                url: actionUrl,
                type: actionType,
                data: form,
                cache: false,
                processData: false,
                contentType: false,
                success: function (data) {
                    //console.log(data);
                    //data: return data from server
                    if (data.status === "error") {
                        icon = "times";
                        messageType = "danger";
                    }

                    //Empty the form fields
                    formElement.find("input[type=text], textarea").val("");
                    //trigger cancel button click event to collapse form and show title of add page
                    $('.btn-collapse').trigger('click');
                    formElement.find('input[name="voucher_value"]').prop('readonly', true);
                    //reload the data in the datatable
                    grid.getDataTable().ajax.reload();
                    Metronic.alert({
                        type: messageType,
                        icon: icon,
                        message: data.message,
                        container: $('#ajax-response-text'),
                        place: 'prepend',
                        closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                    });
                },
                error: function (jqXhr, json, errorThrown) {
                    var errors = jqXhr.responseJSON;
                    var errorsHtml = '';
                    $.each(errors, function (key, value) {
                        errorsHtml += value[0] + '<br />';
                    });
                    //alert(errorsHtml, "Error " + jqXhr.status + ': ' + errorThrown);
                    Metronic.alert({
                        type: 'danger',
                        message: errorsHtml,
                        container: $('#ajax-response-text'),
                        place: 'prepend',
                        closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                    });
                }
            }
        );
    }

    var handleTable = function () {

        grid = new Datatable();
        grid.init({
            src: $('#product-merchant-table'),
            loadingMessage: 'Loading...',
            dataTable: {
                'language': {
                    'info': '<span class="seperator">|</span><b>Total _TOTAL_ record(s) found</b>',
                    'infoEmpty': '',
                },
                "bStateSave": false,
                "lengthMenu": siteObjJs.admin.commonJs.constants.gridLengthMenu,
                "pageLength": siteObjJs.admin.commonJs.constants.recordsPerPage,
                "columns": [
                    { data: null, name: 'rownum', searchable: false, orderable: false },
                    { data: 'id', name: 'id', visible: false, searchable: false, orderable: false },
                    { data: 'product_logo', name: 'product_logo', searchable: false, orderable: false },
                    { data: 'merchant_name', name: 'merchant_name', orderable: false },
                    { data: 'product_name', name: 'product_name', orderable: false },
                    { data: 'brand_name', name: 'brand_name', orderable: false },
                    { data: 'sub_product_name_title', name: 'sub_product_name', orderable: false },
                    { data: 'voucher_expiry', name: 'voucher_expiry', orderable: false },
                    { data: 'selling_price', name: 'selling_price', orderable: false },
                    { data: 'special_price', name: 'special_price', orderable: false },
                    { data: 'display_custom_text_or_date', name: 'display_custom_text_or_date', orderable: false },
                    { data: 'custom_text', name: 'custom_text', orderable: false },
                    { data: 'views_button', name: 'views_button', orderable: false },
                    { data: 'status', name: 'status', orderable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                "drawCallback": function (settings) {
                    var api = this.api();
                    var rows = api.rows({ page: 'current' }).nodes();
                    var last = null;
                    var page = api.page();
                    var recNum = null;
                    var displayLength = settings._iDisplayLength;
                    api.column(0, { page: 'current' }).data().each(function (group, i) {
                        recNum = ((page * displayLength) + i + 1);
                        $(rows).eq(i).children('td:first-child').html(recNum);
                    });

                    api.column(12, { page: 'current' }).data().each(function (group, i) {
                        var status = $(rows).eq(i).children('td:nth-child(13)').html();
                        var statusBtn = '';
                        if (status == 1) {
                            statusBtn = '<span class="label label-sm label-success">active</span>';
                        } else {
                            statusBtn = '<span class="label label-sm label-danger">inactive</span>';
                        }
                        $(rows).eq(i).children('td:nth-child(13)').html(statusBtn);
                    });
                },
                "ajax": {
                    "url": "products-merchant/data",
                    "type": "GET"
                },
                "order": [
                    [1, "desc"]
                ]// set first column as a default sort by asc
            }
        });
        $('#data-search').keyup(function () {
            grid.getDataTable().search($(this).val()).draw();
        });

        $(".form-filter-attr").keyup(function (e) {
            var code = e.which; // recommended to use e.which, it's normalized across browsers
            if (code == 13)
                e.preventDefault();
            if (code == 32 || code == 13 || code == 188 || code == 186) {
                $('.filter-submit').click();
            }
        });
        // For drop down filter
        $(".form-filter-select-attr").change(function () {
            $('.filter-submit').click();
        })
    };

    var fetchProductDataForView = function () {
        $('.portlet-body').on('click', '.view-product-form-link', function () {

            var formElement = $('#merchant_type').closest("form");
            var formId = formElement.attr("id");
            var productId = $(this).attr("id");

            var actionUrl = adminUrl + '/products-merchant/view-merchant-product-details/' + productId;
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#view-merchant-name').html('');
                    $('#view-product-master').html();
                    $('#view-sub-product-name').html('');
                    $('#view-brand-name').html('');
                    $('#view-short-description').html('');
                    $('#view-expiry-date').html('');
                    $('#view-voucher-value').html('');
                    $('#view-selling-price').html('');
                    $('#view-selling-price-points-value').html('');
                    $('#view-special-price').html('');
                    $('#view-special-price-points-value').html('');
                    $('#view-special-price-start-date').html('');
                    $('#view-special-price-end-date').html('');
                    $('#view-discount-percentage').html('');
                    $('#view-min-quantity').html('');
                    $('#view-max-quantity').html('');
                    $('#view-notify-for-qty-below').html('');
                    $('#view-stock-availability').html('');
                    $('#view-status').html('');

                    $("#view-product-details-modal").modal();
                    $('#view-merchant-name').html(data.merchant_name);
                    $('#view-product-master').html(data.product_name);

                    if (data.sub_product_name_title != '') {
                        $('#view-sub-product-name').html(data.sub_product_name_title);
                    } else {
                        $('#view-sub-product-name').html('-');
                    }

                    if (data.brand_name != '') {
                        $('#view-brand-name').html(data.brand_name);
                    } else {
                        $('#view-brand-name').html('-');
                    }
                    $('#view-sku').html(data.sku);
                    if (data.short_description != '') {
                        $('#view-short-description').html(data.short_description);
                    } else {
                        $('#view-short-description').html('-');
                    }

                    if (data.expiry_date != '' && data.expiry_date != null) {
                        $('#view-expiry-date').html(data.expiry_date);
                    } else {
                        $('#view-expiry-date').html('-');
                    }

                    if (data.voucher_value != '' && data.voucher_value != '0.0000') {
                        $('#view-voucher-value').html(parseFloat(data.voucher_value).toFixed(2));
                    } else {
                        $('#view-voucher-value').html('-');
                    }
                    if (data.selling_price != '' && data.selling_price != '0.0000') {
                        $('#view-selling-price').html(parseFloat(data.selling_price).toFixed(2));
                    } else {
                        $('#view-selling-price').html('-');
                    }

                    if (data.selling_price_points_value != '' && data.selling_price_points_value != '0.0000') {
                        $('#view-selling-price-points-value').html(parseFloat(data.selling_price_points_value).toFixed(2));
                    } else {
                        $('#view-selling-price-points-value').html('-');
                    }

                    if (data.special_price != '' && data.special_price != '0.0000') {
                        $('#view-special-price').html(parseFloat(data.special_price).toFixed(2));
                    } else {
                        $('#view-special-price').html('-');
                    }

                    if (data.special_price_points_value != '' && data.special_price_points_value != '0.0000') {
                        $('#view-special-price-points-value').html(parseFloat(data.special_price_points_value).toFixed(2));
                    } else {
                        $('#view-special-price-points-value').html('-');
                    }

                    if (data.special_price_start_date != '' && data.special_price_start_date != null) {
                        $('#view-special-price-start-date').html(data.special_price_start_date);
                    } else {
                        $('#view-special-price-start-date').html('-');
                    }

                    if (data.special_price_end_date != '' && data.special_price_end_date != null) {
                        $('#view-special-price-end-date').html(data.special_price_end_date);
                    } else {
                        $('#view-special-price-end-date').html('-');
                    }

                    if (data.discount_percentage != '' && data.discount_percentage != '0.0000') {
                        $('#view-discount-percentage').html(parseFloat(data.discount_percentage).toFixed(2) + ' %');
                    } else {
                        $('#view-discount-percentage').html('-');
                    }

                    if (data.min_quantity != '') {
                        $('#view-min-quantity').html(data.min_quantity);
                    } else {
                        $('#view-min-quantity').html('-');
                    }

                    if (data.max_quantity != '') {
                        $('#view-max-quantity').html(data.max_quantity);
                    } else {
                        $('#view-max-quantity').html('-');
                    }

                    if (data.notify_for_qty_below != '') {
                        $('#view-notify-for-qty-below').html(data.notify_for_qty_below);
                    } else {
                        $('#view-notify-for-qty-below').html('-');
                    }

                    if (data.stock_availability == '1') {
                        $('#view-stock-availability').html('In Stock');
                    } else {
                        $('#view-stock-availability').html('Out of Stock');
                    }

                    if (data.pay_for_product_in == '0') {
                        $('#view-pay-for-product-in').html('Both');
                    } else if (data.pay_for_product_in == '1') {
                        $('#view-pay-for-product-in').html('Points Unit Only (Eg: points or stars etc.)');
                    } else if (data.pay_for_product_in == '2') {
                        $('#view-pay-for-product-in').html('Currency Only (Eg: INR, USD etc.)');
                    }

                    if (data.status == '1') {
                        $('#view-status').html('Active');
                    } else {
                        $('#view-status').html('Inactive');
                    }

                },
                error: function (jqXhr, json, errorThrown) {

                }
            });
        });
    };

    var fetchProductImagesForView = function () {
        $('.portlet-body').on('click', '.view-product-images-form-link', function () {
            var formElement = $('#merchant_type').closest("form");
            var formId = formElement.attr("id");
            var productId = $(this).attr("id");

            var actionUrl = adminUrl + '/products-merchant/view-merchant-product-images/' + productId;
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                processData: false,
                contentType: false,
                success: function (data) {
                    $("#view-product-image-details-modal").modal();
                    $("#indicators").html(data.carousal_indicators);
                    $("#slider-images").html(data.carousal_inner);

                },
                error: function (jqXhr, json, errorThrown) {

                }
            });
        });
    };

    var fetchProductLocationInventory = function () {
        $('.portlet-body').on('click', '.view-product-location-inventory-form-link', function () {
            var formElement = $('#merchant_type').closest("form");
            var formId = formElement.attr("id");
            var productId = $(this).attr("id");

            var actionUrl = adminUrl + '/products-merchant/view-merchant-product-location-inventory/' + productId;
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                processData: false,
                contentType: false,
                success: function (data) {

                    $('#location-inventory-table > tbody').empty();
                    $("#view-product-location-inventory-modal").modal();
                    var locationRows = data.totalCnt;

                    if (locationRows > 0) {
                        for (var i = 0, sr_no = 1; i < locationRows; i++, sr_no++) {
                            $('.view_location_rows').append("<tr>" +
                                "<td>" + sr_no + " </td>" +
                                "<td>" + data[i].location_name + "</td>" +
                                "<td>" + data[i].current_quantity + "</td>" +
                                "</tr>");
                        }
                    } else {
                        $('.view_location_rows').append("<tr>" +
                            "<td colspan='3' align='center'>No data available in table</td>" +
                            "</tr>");
                    }


                },
                error: function (jqXhr, json, errorThrown) {

                }
            });
        });
    };


    var fetchProductInventory = function () {
        $('.portlet-body').on('click', '.view-product-inventory-form-link', function () {
            var formElement = $('#merchant_type').closest("form");
            var formId = formElement.attr("id");
            var productId = $(this).attr("id");

            var actionUrl = adminUrl + '/products-merchant/view-merchant-product-inventory/' + productId;
            $.ajax({
                url: actionUrl,
                cache: false,
                dataType: "json",
                type: "GET",
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#product-inventory-table').empty();
                    $("#view-product-inventory-modal").modal();
                    $('#product-inventory-table').append(data);
                },
                error: function (jqXhr, json, errorThrown) {

                }
            });
        });
    };


    var handleBootstrapMaxlength = function (formId) {
        $('#' + formId).find("textarea").maxlength({
            limitReachedClass: "label label-danger",
            alwaysShow: true,
            placement: 'bottom-left',
            threshold: 10
        });
    };

    var handleDatePicker = function () {
        if (!jQuery().datepicker) {
            return;
        }
        $(".form_datetime").datepicker({
            autoclose: true,
            isRTL: true,
            format: "dd MM yyyy",
            startDate: '+0d',
            pickerPosition: "bottom-left"
        })
    };

    $.validator.addMethod('numberOnly', function (value) {
        return Number(value) >= 0;
    }, 'Please enter Numbers Only.');

    $.validator.addMethod('greaterThanZero', function (value, element) {
        return this.optional(element) || (parseFloat(value) > 0);
    }, 'Number must be greater than zero.');

    $.validator.addMethod('priceRangeValid', function (value, element) {
        var formElement = $(element).closest("form");
        var formId = formElement.attr("id");
        var minVal = $('#' + formId).find('#selling_price').val();

        if (value != '') {
            return (parseFloat(value) < parseFloat(minVal));
        } else {
            return true;
        }

    }, 'Special Price must be smaller than Selling Price.');

    // $.validator.addMethod('startDateValid', function (value, element) {
    //     var formElement = $(element).closest("form");
    //     var formId = formElement.attr("id");
    //     var minVal = $('#' + formId).find('#special_price').val();
    //     if (minVal != '' && value == '') {
    //         return false;
    //     } else {
    //         return true;
    //     }

    // }, 'Please select Special Price Start Date.');

    // $.validator.addMethod('endDateValid', function (value, element) {
    //     var formElement = $(element).closest("form");
    //     var formId = formElement.attr("id");
    //     var minVal = $('#' + formId).find('#special_price').val();
    //     if (minVal != '' && value == '') {
    //         return false;
    //     } else {
    //         return true;
    //     }

    // }, 'Please select Special Price End Date.');

    $.validator.addMethod('startDateValid', function (value, element) {

        var formElement = $(element).closest("form");
        var formId = formElement.attr("id");
        var minVal = $('#' + formId).find('#special_price').val();
        if (value == '' && minVal != '') {
            return false;
        }
        $('#' + formId).find('#special_price_end_date').attr('min', value);
        return true;

    }, 'Please select Special Price Start Date.');

    $.validator.addMethod('endDateValid', function (value, element) {
        var formElement = $(element).closest("form");
        var formId = formElement.attr("id");
        var startDate = $('#' + formId).find('#special_price_start_date').val();
        var minVal = $('#' + formId).find('#special_price').val();
        if ((value == '' || startDate == '') && minVal != '') {
            return false;
        }
        var endDate = new Date(value);
        var startDate = new Date(startDate);
        if (startDate > endDate) {
            return false;
        }
        return true;

    }, 'Please select Special Price End Date.');


    $.validator.addMethod('quantityValid', function (value, element) {
        var formElement = $(element).closest("form");
        var formId = formElement.attr("id");
        var minVal = $('#' + formId).find('#min_quantity').val();
        return (parseFloat(value) >= parseFloat(minVal));
    }, 'Max Quantity must be greater than or equal Min Quantity.');


    var getLocationsForMerchantId = function () {
        var token = $('meta[name="csrf-token"]').attr('content');
        $('.portlet-body').on('change', '#merchant_id', function () {
            var merchant_id = $(this).val();
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            //var id = $(this).closest('.remove-location-id-div').attr('id');
            $('#' + formId).find(".remove-location-id-div").remove();

            $('#' + formId + ' #label_location_id_1').html('Location <span class="required" aria-required="true">*</span>');
            $('#' + formId + ' #label_quantity_id_1').html('Opening Quantity <span class="required" aria-required="true">*</span>');

            var product_id = $('#' + formId + ' #product_id').val();
            var location_id = $('#' + formId + ' #location_id_1').val();

            if (merchant_id != '' && product_id != '') {
                $('#' + formId).find('#add-merchant-location').attr('disabled', false);
            } else {
                $('#' + formId).find('#add-merchant-location').attr('disabled', true);
            }
            if (merchant_id > 0) {
                var actionUrl = adminUrl + '/products-merchant/get-locations/' + merchant_id;
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    dataType: "json",
                    type: "GET",
                    success: function (data) {
                        var $el = $('form').find("#location_id_1");
                        if (data != '') {
                            //$el.select2("val", '');
                            $el.empty(); // remove old options
                            $el.append($('<option>', {
                                value: 'All',
                                text: 'All Locations',
                            }));
                            $.each(data, function (value, key) {
                                $el.append($('<option>', {
                                    value: value,
                                    text: key,
                                }));
                            });
                        } else {
                            //$el.select2("val", '');
                            $('#' + formId).find('#add-merchant-location').attr('disabled', true);
                            $el.empty(); // remove old options
                            $el.append($("<option></option>").attr("value", 'All').text('All Locations'));
                        }
                    },
                    error: function (jqXhr, json, errorThrown) {
                        var errors = jqXhr.responseJSON;
                        var errorsHtml = '';
                        $.each(errors, function (key, value) {
                            errorsHtml += value[0] + '<br />';
                        });
                        // alert(errorsHtml, "Error " + jqXhr.status + ': ' + errorThrown);
                        Metronic.alert({
                            type: 'danger',
                            message: errorsHtml,
                            container: $('#ajax-response-text'),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    }
                });

            }
        });
    };

    //get brands from Merchant-Location-Brand relation for given Merchant Id
    var getBrandsForMerchantId = function () {
        var token = $('meta[name="csrf-token"]').attr('content');
        $('.portlet-body').on('change', '#merchant_id', function () {
            var merchant_id = $(this).val();
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            //var id = $(this).closest('.remove-location-id-div').attr('id');
            $('#' + formId).find(".remove-location-id-div").remove();
            var $eloc = $('form').find("#location_id_1");
            $eloc.empty(); // remove old options
            $eloc.append($('<option>', {
                value: '',
                text: 'Select Locations',
            }));
            if (merchant_id > 0) {
                var actionUrl = adminUrl + '/products-merchant/get-brands/' + merchant_id;
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    dataType: "json",
                    type: "GET",
                    success: function (data) {
                        //fill up brands in select list   
                        var $el = $('form').find("#brand_id");
                        $el.select2("val", '');
                        if (data.brands !== '') {
                            $el.empty(); // remove old options    
                            $el.append($('<option>', {
                                value: '',
                                text: 'Select Brand',
                            }));
                            $.each(data.brands, function (value, key) {
                                $el.append($('<option>', {
                                    value: key.id,
                                    text: key.brand_name,
                                }));
                            });
                        } else {
                            $('#' + formId).find('#add-merchant-location').attr('disabled', true);
                            $el.empty(); // remove old options                            
                        }
                    },
                    error: function (jqXhr, json, errorThrown) {
                        var errors = jqXhr.responseJSON;
                        var errorsHtml = '';
                        $.each(errors, function (key, value) {
                            errorsHtml += value[0] + '<br />';
                        });
                        // alert(errorsHtml, "Error " + jqXhr.status + ': ' + errorThrown);
                        Metronic.alert({
                            type: 'danger',
                            message: errorsHtml,
                            container: $('#ajax-response-text'),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    }
                });

            }
        });
    };

    //get locations from Merchant-Location-Brand relation for Merchant Id and Brand Id selected
    var getLocationsForBrandAndMerchantId = function () {
        var token = $('meta[name="csrf-token"]').attr('content');
        $('.portlet-body').on('change', '#brand_id', function () {
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            var brand_id = $(this).val();
            var merchant_id = $('#' + formId).find('#merchant_id').val();
            //var id = $(this).closest('.remove-location-id-div').attr('id');
            $('#' + formId).find(".remove-location-id-div").remove();
            $('#' + formId + ' #label_location_id_1').html('Location <span class="required" aria-required="true">*</span>');
            $('#' + formId + ' #label_quantity_id_1').html('Opening Quantity <span class="required" aria-required="true">*</span>');

            var product_id = $('#' + formId + ' #product_id').val();
            var location_id = $('#' + formId + ' #location_id_1').val('');

            if (merchant_id != '' && product_id != '') {
                $('#' + formId).find('#add-merchant-location').attr('disabled', false);
            } else {
                $('#' + formId).find('#add-merchant-location').attr('disabled', true);
            }
            if (merchant_id > 0 && brand_id > 0) {
                var actionUrl = adminUrl + '/products-merchant/get-locations-for-merchant-brand/' + merchant_id + '/' + brand_id;
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    dataType: "json",
                    type: "GET",
                    success: function (data) {
                        //fill up locations in select list   
                        var $el = $('form').find("#location_id_1");
                        if (data !== '') {
                            //$el.select2("val", '');
                            $el.empty(); // remove old options
                            $el.append($('<option>', {
                                value: '',
                                text: 'Select Locations',
                            }));
                            $.each(data.locations, function (value, key) {
                                $el.append($('<option>', {
                                    value: value,
                                    text: key,
                                }));
                            });
                        } else {
                            //$el.select2("val", '');
                            $('#' + formId).find('#add-merchant-location').attr('disabled', true);
                            $el.empty(); // remove old options
                            $el.append($("<option></option>").attr("value", '').text('Select Locations'));
                        }
                    },
                    error: function (jqXhr, json, errorThrown) {
                        var errors = jqXhr.responseJSON;
                        var errorsHtml = '';
                        $.each(errors, function (key, value) {
                            errorsHtml += value[0] + '<br />';
                        });
                        // alert(errorsHtml, "Error " + jqXhr.status + ': ' + errorThrown);
                        Metronic.alert({
                            type: 'danger',
                            message: errorsHtml,
                            container: $('#ajax-response-text'),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    }
                });

            }
        });
    };

    var showLocationQtyOnEditForm = function (product_merchant_id) {
        var token = $('meta[name="csrf-token"]').attr('content');
        var actionUrl = 'products-merchant/get-locations-qty/' + product_merchant_id;
        $.ajax({
            url: actionUrl,
            cache: false,
            dataType: "json",
            type: "GET",
            success: function (data) {

                var locqty_rows = '';
                if (data.locations !== '') {
                    $.each(data.locations, function (value, key) {
                        locqty_rows = locqty_rows + '<tr><td class="col-md-4">' + key.location_name + '</td><td class="col-md-4">' + key.quantity + '</tr>';
                    });
                    var tableRows = '<table class="table-bordered col-md-12"><thead><tr><td class="col-md-4">Name</td><td class="col-md-4">Quantity</td></tr></thead><tbody>' + locqty_rows + '</tbody></table>';
                    $('#edit-product-merchant').find("#show-locations").html(tableRows);
                } else {
                    $('#edit-product-merchant').find("#show-locations").html('');
                }

            },
            error: function (jqXhr, json, errorThrown) {
                var errors = jqXhr.responseJSON;
                var errorsHtml = '';
                $.each(errors, function (key, value) {
                    errorsHtml += value[0] + '<br />';
                });
                // alert(errorsHtml, "Error " + jqXhr.status + ': ' + errorThrown);
                Metronic.alert({
                    type: 'danger',
                    message: errorsHtml,
                    container: $('#ajax-response-text'),
                    place: 'prepend',
                    closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                });
            }
        });

    };


    var addLocationRow = function () {
        $('#add-merchant-location').live('click', function (e) {

            e.preventDefault();
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            var merchant_id = $('#' + formId + ' #merchant_id').val();


            var javascriptObject = {};
            $('#' + formId + ' #location_id_1 > option:gt(0)').each(function () {
                javascriptObject[this.value] = $(this).text();
            });

            var totalDashItem = $('#' + formId + ' #total-merchant-location').val();
            var nextTotaDashItem = parseInt(totalDashItem) + 1;

            var selectedItems = [];
            $('#' + formId).find('.select-location-item  option:selected').each(function () {
                selectedItems[$(this).val()] = $(this).val();
            });
            selectedItems = selectedItems.filter(function (n) {
                return n != undefined
            });

            $('#' + formId).find('#merchan-location-div').append('<div class="row remove-location-id-div" id="remove-location-id-div-' + nextTotaDashItem + '">' +
                '<div class="col-md-6">' +
                '<div class="form-group">' +
                '<label class="col-md-4 control-label" id="label_location_id_' + nextTotaDashItem + '">Location ' + nextTotaDashItem + ' <span class="required" aria-required="true">*</span></label>' +
                '<div class="col-md-8">' +
                '<select name="location_id[' + nextTotaDashItem + ']" class="form-control select-location-item count-location-id" required="true" data-msg="Please select Location." id="location_id_' + nextTotaDashItem + '">' +
                '<option value="">Select Location</option>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                '<div class="form-group">' +
                '<label class="col-md-4 control-label" id="label_quantity_id_' + nextTotaDashItem + '">Opening Quantity ' + nextTotaDashItem + ' <span class="required" aria-required="true">*</span></label>' +
                '             <div class="col-md-6">' +
                '<input type="text" name="current_quantity[' + nextTotaDashItem + ']" id="current_quantity_' + nextTotaDashItem + '" class="form-control" maxlength="10" required="true" data-msg="Please enter Opening Quantity." greaterThanZero = "true" numberOnly="true">' +
                '</div>' +
                '<div class="col-md-2">' +
                '<button type="button" class="remove-location-id btn btn-icon-only red" id="remove_location_id_' + nextTotaDashItem + '"><i class="fa fa-times"></i></button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>');


            var $el = $("#location_id_" + nextTotaDashItem);
            $el.empty(); // remove old options
            $el.append($("<option></option>").attr("value", '').text('Select Location ' + nextTotaDashItem));

            var optionCnt = 0;
            $.each(javascriptObject, function (value, key) {
                if ($.inArray(value, selectedItems) == -1) {
                    optionCnt++;
                    $el.append($("<option></option>").attr("value", value).text(key));
                } else {
                    $el.append($("<option style='display:none'></option>").attr("value", value).text(key));
                }

            });

            var options = $("#location_id_" + nextTotaDashItem + " option:gt(0)");
            options.detach().sort(function (a, b) {
                var at = $(a).text();
                var bt = $(b).text();
                return (at > bt) ? 1 : ((at < bt) ? -1 : 0);
            });
            options.appendTo("#location_id_" + nextTotaDashItem);

            $('#' + formId + ' #add-merchant-location' + totalDashItem).hide();
            $('#' + formId + ' #total-merchant-location').val(nextTotaDashItem);
            $('#' + formId + ' #label_location_id_1').html('Location 1<span class="required" aria-required="true">*</span>');
            $('#' + formId + ' #label_quantity_id_1').html('Opening Quantity 1<span class="required" aria-required="true">*</span>');

            var wECount = $('#' + formId + " select[class*='count-location-id']").length;

            var max = 0;
            $('#' + formId + ' .remove-location-id').each(function () {
                id = this.id.match(/\d+/);

                if (id > max) {
                    max = id;
                }

                if ((parseInt(max[0])) == wECount) {
                    $(this).css('display', 'inline');
                } else {
                    $(this).css('display', 'none');
                }
            });

            wECount = parseInt(wECount) - 1;

        });


        $('.portlet-body').on('change', '.select-location-item', function (e) {
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");

            var location_id = $('#' + formId + ' #location_id_1').val();


            if (location_id != 'All') {
                if ($('select#location_id_1 option').length > 2) {
                    $('#' + formId).find('#add-merchant-location').attr('disabled', false);
                }
            } else {
                $('.remove-location-id-div').remove();
                $('#' + formId).find('#add-merchant-location').attr('disabled', true);
            }
        });

        $('.portlet-body').on('change', '.change-product-id', function (e) {
            var master_id = $(this).val();
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");

            var actionUrl = adminUrl + '/products-merchant/check-product-master-is-voucher/' + master_id;
            if (master_id > 0) {
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    dataType: "json",
                    type: "GET",
                    success: function (data) {
                        //console.log(data.purchase_type);
                        if (data.purchase_type == 1) {
                            $('#' + formId).find('input[name="voucher_value"]').prop('readonly', false);
                        }
                    },
                    error: function (jqXhr, json, errorThrown) {
                        var errors = jqXhr.responseJSON;
                        var errorsHtml = '';
                        $.each(errors, function (key, value) {
                            errorsHtml += value[0] + '<br />';
                        });
                        // alert(errorsHtml, "Error " + jqXhr.status + ': ' + errorThrown);
                        Metronic.alert({
                            type: 'danger',
                            message: errorsHtml,
                            container: $('#ajax-response-text'),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    }
                });

            }

        });

    };
    var getproductData = function (currentForm) {

        $('body').on('change', "#category_id", function (e) {

            productDataCommon(currentForm);

        });
    };

    var productDataCommon = function (currentForm) {
        var actionUrl = adminUrl + '/baskets/get-products';

        var categoryId = $('#category_id').val();
        var categoryName = $('#category_id option:selected').text();
        if (categoryName === 'Please select') {
            categoryName = '';
        }
        var formData = new FormData();
        formData.append('category_id', categoryId);
        formData.append('category_name', categoryName);
        $.ajax({
            url: actionUrl,
            cache: false,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (data) {
                var $ell = $('#' + currentForm).find("#productUnits");
                $ell.empty();
                $ell.select2("val", '');
                $ell.empty(); // remove old options  

                $.each(data.product_details, function (value, key) {
                    $ell.append($('<option>', {
                        value: value,
                        text: key,
                    }));
                });
            },
            error: function (jqXhr, json, errorThrown) {
                var errors = jqXhr.responseJSON;
                var errorsHtml = '';
                $.each(errors, function (key, value) {
                    errorsHtml += value[0] + '<br />';
                });
                Metronic.alert({
                    type: 'danger',
                    message: errorsHtml,
                    container: $('#ajax-response-text'),
                    place: 'prepend',
                    closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                });
            }
        });

    }

    var removeLocationRow = function () {
        $('.remove-location-id').live('click', function (e) {
            var formId = $(this).closest("form").attr("id");
            var id = $(this).closest('.remove-location-id-div').attr('id');
            $("#" + id).remove();

            var totalCatApp = $('#' + formId + ' .total-merchant-location').val();

            var nextTotalCatApp = parseInt(totalCatApp) - 1;
            $('#' + formId + ' .total-merchant-location').val(nextTotalCatApp);
            //$('#' + formId + ' #add-loyalty-program-categorywise').text('Add Category ' + (parseInt(nextTotalCatApp) + 1));

            var max = 0;
            $('#' + formId + ' .remove-location-id').each(function () {
                id = this.id.match(/\d+/);

                if (id > max) {
                    max = id;
                }

                if (max[0] == nextTotalCatApp) {
                    $(this).css('display', 'inline');
                } else {
                    $(this).css('display', 'none');
                }
            });

            //$('#' + formId + ' #add_dashboard_item_' + nextTotalCatApp).show();
        });

    };

    var changeSpecialPrice = function (formId) {
        $('#' + formId).on('change', '#special_price', function () {
            var special_price = $(this).val();
            if (!special_price) {
                $('#' + formId).find("#special_price_start_date").datepicker({
                    autoclose: true,
                    isRTL: Metronic.isRTL(),
                    format: "dd MM yyyy",
                    startDate: '+0d',
                    pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left")
                }).val('');
                $('#' + formId).find("#special_price_end_date").datepicker({
                    autoclose: true,
                    isRTL: Metronic.isRTL(),
                    format: "dd MM yyyy",
                    startDate: '+0d',
                    pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left")
                }).val('');
            }
        });
    };

    return {
        //main function to initiate the module
        init: function (formId) {

            initializeListener(formId);
            // handleTable();
            fetchDataForEdit();
            handleBootstrapMaxlength(formId);
            handleDatePicker();
            //getLocationsForMerchantId();            
            getBrandsForMerchantId();
            getLocationsForBrandAndMerchantId();
            addLocationRow();
            removeLocationRow();
            fetchProductDataForView();
            fetchProductImagesForView();
            fetchProductLocationInventory();
            fetchProductInventory();
            changeSpecialPrice('create-product');
            handleCustomTextEvents('create-product');
            getproductData(formId);
            if (formId === 'create-basket') {
                productDataCommon(formId);
            }

            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#create-product', handleAjaxRequest);
        }

    };
}();