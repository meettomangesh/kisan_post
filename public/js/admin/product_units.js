siteObjJs.admin.productUnitJs = function () {
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
        $('body').on("click", ".btn-expand-form", function () {
            storedFiles = [];
            storedNewFiles = [];
        });
        $('body').on("click", ".portlet-title", function () {
            storedFiles = [];
            storedNewFiles = [];
        });

        $('#product_image').bind('change', function (e) {
            //this.files[0].size gets the size of your file.
            if (this.files[0]) {
                if (this.files[0].size > 2097152) {
                    var error = siteObjJs.admin.validateUserJs.maxFileSize;
                    $('#file-error').text(error);
                    return false;
                }

                var ext = $('#product_image').val().split('.').pop().toLowerCase();
                if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                    var error = siteObjJs.admin.validateUserJs.mimes;
                    $('#file-error').text(error);
                    return false;
                } else
                {
                    $('#file-error').text('');
                }

            }

        });

        //if "remove" button is clicked from input, clear error messages
        $("a.fileinput-exists").on('click', function () {
            $('#file-error').text('');
        });

        $('#selling_price').on('change', function (e) {
            $('#selling_price_points_value').val($(this).val());
        });

        $('#special_price').on('change', function (e) {
            $('#special_price_points_value').val($(this).val());
        });

        $('#edit-product').on('submit', function() {
            if($("#dvPreview").children().length == 0 && $('input[type="file"]').val() == '') {
                $("#edit-product span#file-error-container").attr("style", "color: red").text("Please select at least one image.").addClass('help-block-error red');
                setTimeout(function(){
                    $("#edit-product span#file-error-container").text("").removeClass('help-block-error');
                }, 3000);
                return false;
            }
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
                    success: function (data)
                    {
                        //console.log(data);
                        //data: return data from server
                        if (data.status === "error")
                        {
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
                    error: function (jqXhr, json, errorThrown)
                    {
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
    
    var handleBootstrapMaxlength = function (formId) {
        $('#'+formId).find("textarea").maxlength({
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
            handleBootstrapMaxlength(formId);
            handleDatePicker();
            changeSpecialPrice(formId);
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#'+formId, handleAjaxRequest);
        }

    };
}();