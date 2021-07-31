siteObjJs.admin.communicationMessageJs = function () {

    // Initialize all the page-specific event listeners here.

    var initializeListener = function () {

        //CKEDITOR.replace( 'email_body' );

        $('body').on("click", ".btn-collapse", function () {
            $("#ajax-response-text").html("");
            //retrieve id of form element and create new instance of validator to clear the error messages if any
            var formElement = $(this).closest("form");
            var formId = formElement.attr("id");
            $("#" + formId + " .select2me").select2("val", "");

            var validator = $('#' + formId).validate();
            validator.resetForm();
            formElement.find('#pos_ids').tagsinput('removeAll');
            //remove any success or error classes on any form, to reset the label and helper colors
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');
            $("#" + formId).find('#today_time-error').html('');
        });


    };
    // Method to fetch and place edit form with data using ajax call

    var fetchDataForEdit = function (obj) {
        if (obj === 'edit-communication') {
            var regionTypeFlag = $('#' + obj).find('input[name="region_type"]:checked').val();
            var userTypeFlag = $('#' + obj).find('input[name="user_type"]:checked').val();
            var emailFlag = $('#' + obj).find('input[id="email"]:checked').val();
            var smsFlag = $('#' + obj).find('input[id="sms"]:checked').val();
            var pushFlag = $('#' + obj).find('input[id="push_notification"]:checked').val();

            if (regionTypeFlag === '2') {
                $('#' + obj).find('#region-selection-div').show();
            } else {
                $('#' + obj).find('#region-selection-div').hide();
            }

            if (userTypeFlag === '2') {
                $('#' + obj).find('#user-selection-div').show();
            } else {
                $('#' + obj).find('#user-selection-div').hide();
            }
            if ($('#' + obj).find('#email').is(":checked")) {
                $('#' + obj).find('#email-div').show();
            } else {
                $('#' + obj).find('#email-div').hide();
            }
            if ($('#' + obj).find('#sms').is(":checked")) {
                $('#' + obj).find('#sms-text-div').show();
            } else {
                $('#' + obj).find('#sms-text-div').hide();
            }
            if ($('#' + obj).find('#push_notification').is(":checked")) {
                $('#' + obj).find('#push-text-div').show();
            } else {
                $('#' + obj).find('#push-text-div').hide();
            }


        }
    };



    // method to handle add ajax request and reponse
    var handleAjaxRequest = function (form, e) {

        var currentForm1 = $(form);
        if (currentForm1.find('#email').is(':checked') || currentForm1.find('#push_notification').is(':checked') || currentForm1.find('#sms').is(':checked') || currentForm1.find('#sms_notification').is(':checked')) {

        } else {

            var error = 'Please select atleast one Message Notify By.';
            Metronic.alert({
                type: 'danger',
                icon: 'times',
                message: error,
                container: $('#ajax-response-text'),
                place: 'prepend',
                closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
            });
            return false;
        }

        form.submit();
        //$('ol').append('<li>' + $(form[0]).val() + '</li>');
        // validator = form.validate();
        // validator.resetForm();
        // validator.showErrors({ field: 'Validation failed'} );
        // console.log('inside handler');
    };




    var enableTestingModeDiv = function (currentForm) {
        var currentForm = $('#' + currentForm);
        $('body').on('change', '#for_testing', function (e) {
            if ($('body').find('#for_testing').val() == 1) {
                currentForm.find('#testing-mode-div').show();
            } else {
                currentForm.find('#test_email_addresses').val('');
                currentForm.find('#test_mobile_numbers').val('');
                currentForm.find('#testing-mode-div').hide();
            }
            currentForm.find('#send-email-btn').prop('disabled', true);
            currentForm.find('#send-sms-btn').prop('disabled', true);
            currentForm.find('#test_email_addresses').val('');
            currentForm.find('#test_mobile_numbers').val('');
            if (currentForm.find('#email').is(":checked")) {
                currentForm.find('#send-email-btn').prop('disabled', false);
            } else {
                currentForm.find('#test_email_addresses').val('');
            }
            if (currentForm.find('#sms').is(":checked")) {
                currentForm.find('#send-sms-btn').prop('disabled', false);
            } else {
                currentForm.find('#test_mobile_numbers').val('');
            }
            if (currentForm.find('#sms_notification').is(":checked")) {
                currentForm.find('#send-sms-btn').prop('disabled', false);
            } else {
                currentForm.find('#test_mobile_numbers').val('');
            }
            if (!(currentForm.find('#sms_notification').is(":checked") || currentForm.find('#sms').is(":checked"))) {
                currentForm.find('#send-sms-btn').prop('disabled', true);
            }
            if (!currentForm.find('#email').is(":checked")) {
                currentForm.find('#send-email-btn').prop('disabled', true);
            }
        });

    };
    var handleBootstrapMaxlength = function () {
        $('#create-customer-communication-message').find("textarea").maxlength({
            limitReachedClass: "label label-danger",
            alwaysShow: true,
            placement: 'bottom-left',
            threshold: 10
        });

        $('#edit-communication').find("textarea").maxlength({
            limitReachedClass: "label label-danger",
            alwaysShow: true,
            placement: 'bottom-left',
            threshold: 10
        });

    };

    /*var handleDatePicker = function () {
     if (!jQuery().datetimepicker) {
     return;
     }
     
     $(".form_datetime").datetimepicker({
     autoclose: true,
     isRTL: Metronic.isRTL(),
     format: "dd MM yyyy - hh:ii",
     startDate: '+1d',
     endDate: '+3d',
     pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left"),
     });
     
     };*/

    var handleDatePicker = function () {
        if (!jQuery().datepicker) {
            return;
        }

        $(".form_datetime").datepicker({
            autoclose: true,
            isRTL: true,
            format: "dd MM yyyy",
            startDate: '+1d',
            //endDate: '+3d',
            pickerPosition: (true ? "bottom-right" : "bottom-left"),
            onSelect: function (e) {
                alert(e);
                startDate = $(this).datepicker('getDate');
                alert(startDate);
            }
        });

        $(".search_form_datetime").datepicker({
            autoclose: true,
            isRTL: true,
            format: "dd MM yyyy",
            pickerPosition: (true ? "bottom-right" : "bottom-left")
        });

    };

    var showEmailFormDiv = function (currentForm) {
        currentForm = $('#' + currentForm);

        $('body').on('click', '#email', function (e) {
            if ($(this).is(":checked")) {
                currentForm.find('#email-div').show();
            } else {
                currentForm.find('#email-div').hide();
                currentForm.find('#test_email_addresses').val('');
            }
            //send test email sms btn enable disable conditions
            if ($(this).is(":checked")) {
                currentForm.find('#send-email-btn').prop('disabled', false);
                if (currentForm.find('#sms').is(":checked") || currentForm.find('#sms_notification').is(":checked")) {
                    currentForm.find('#send-sms-btn').prop('disabled', false);
                } else {
                    currentForm.find('#send-sms-btn').prop('disabled', true);
                }
            } else {
                currentForm.find('#send-email-btn').prop('disabled', true);
            }
        });


    };

    var showNotificationsFormDiv = function (obj) {
        //var formObj = obj;
        var currentForm = obj;
        currentForm = $('#' + currentForm);

        $('body').on('click', '#push_notification', function (e) {
            if ($(this).is(":checked") || currentForm.find('#sms_notification').is(":checked")) {
                currentForm.find('#push-text-div').show();

                //deep link screen disable for merchant offer
                currentForm.find('#deep_link_screen').prop('disabled', false);
                // currentForm.find("input:radio[name='message_type']").each(function () {
                //     if ($(this).parent().hasClass('checked')) {

                //         currentForm.find('#deep_link_screen').prop('disabled', false);
                //         currentForm.find('#deep_link_screen').select2('val', '');


                //     }
                // });

                currentForm.find('#sms_notification').attr("disabled", true);
            } else if (currentForm.find('#push_notification').is(":checked") || currentForm.find('#sms').is(":checked")) {
                currentForm.find('#sms_notification').attr("disabled", true);
                currentForm.find('#push-text-div').hide();
            } else {
                currentForm.find('#push-text-div').hide();
                currentForm.find('#sms_notification').attr("disabled", false);
            }
            //send test email sms btn enable disable conditions
            if ($(this).is(":checked")) {
                currentForm.find('#send-email-btn').prop('disabled', true);
                currentForm.find('#send-sms-btn').prop('disabled', true);
                if (currentForm.find('#email').is(":checked")) {
                    currentForm.find('#send-email-btn').prop('disabled', false);
                } else {
                    currentForm.find('#send-email-btn').prop('disabled', true);
                }
                if (currentForm.find('#sms').is(":checked") || currentForm.find('#sms_notification').is(":checked")) {
                    currentForm.find('#send-sms-btn').prop('disabled', false);
                } else {
                    currentForm.find('#send-sms-btn').prop('disabled', true);
                }
            }
            //condition when only push notification selected and other options not selected
            if ($(this).is(":checked") && (!(currentForm.find('#sms').is(":checked") || currentForm.find('#sms_notification').is(":checked") || currentForm.find('#email').is(":checked")))) {
                currentForm.find('#send-email-btn').prop('disabled', true);
                currentForm.find('#send-sms-btn').prop('disabled', true);
            }

        });
    };

    var showSmsFormDiv = function (currentForm) {
        currentForm = $('#' + currentForm);

        $('body').on('click', '#sms', function (e) {
            if ($(this).is(":checked") || currentForm.find('#sms_notification').is(":checked")) {
                currentForm.find('#sms-text-div').show();
                currentForm.find('#sms_notification').attr("disabled", true);
            } else if (currentForm.find('#push_notification').is(":checked") || currentForm.find('#sms').is(":checked")) {
                currentForm.find('#sms_notification').attr("disabled", true);
                currentForm.find('#sms-text-div').hide();
                currentForm.find('#test_mobile_numbers').val('');
            } else {
                currentForm.find('#sms-text-div').hide();
                currentForm.find('#test_mobile_numbers').val('');
                currentForm.find('#sms_notification').attr("disabled", false);
            }

            //send test email sms btn enable disable conditions                        
            if ($(this).is(":checked") || currentForm.find('#sms_notification').is(":checked")) {
                currentForm.find('#send-sms-btn').prop('disabled', false);
                if (currentForm.find('#email').is(":checked")) {
                    currentForm.find('#send-email-btn').prop('disabled', false);
                } else {
                    currentForm.find('#send-email-btn').prop('disabled', true);
                }
            } else {
                currentForm.find('#send-sms-btn').prop('disabled', true);
            }

        });
    };

    var showRegionFormDiv = function (currentForm) {
        currentForm = $('#' + currentForm);

        $('body').on('change', "input:radio[name='region_type']", function (e) {

            if ($(this).val() === '2') {
                currentForm.find('#region-selection-div').show();
            } else {
                currentForm.find('#region-selection-div').hide();
            }

        });
    };

    var showUserFormDiv = function (currentForm) {
        currentForm = $('#' + currentForm);

        $('body').on('change', "input:radio[name='user_type']", function (e) {

            if ($(this).val() === '2') {
                currentForm.find('#user-selection-div').show();
            } else {
                currentForm.find('#user-selection-div').hide();
            }

        });
    };

    var userDataCommon = function (currentForm) {
        var actionUrl = adminUrl + '/communications/get-user-type-data';
        var regionTypeFlag = $('input[name="region_type"]:checked').val();
        var customRegion = $('#regions').val();
        var userTypeFlag = $('input[name="user_role"]:checked').val();

        var formData = new FormData();
        formData.append('user_type', userTypeFlag);
        formData.append('custom_region', customRegion);
        formData.append('region_type', regionTypeFlag);
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
                var $ell = $('#' + currentForm).find("#users");
                $ell.empty();
                $ell.select2("val", '');
                $ell.empty(); // remove old options  

                $.each(data.user_details, function (value, key) {
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
    var getUserTypeData = function (currentForm) {


        $('body').on('change', "input:radio[name='user_role']", function (e) {
            if ($(this).val() > 0) {
                userDataCommon(currentForm);
            }
        });

        $('body').on('change', "input:radio[name='region_type']", function (e) {

            userDataCommon(currentForm);

        });

        $('body').on('change', "#regions", function (e) {

            userDataCommon(currentForm);

        });
    };




    var showSmsAndNotificationsFormDiv = function (currentForm) {
        currentForm = $('#' + currentForm);

        $('body').on('click', '#sms_notification', function (e) {
            if ($(this).is(":checked") || (currentForm.find('#push_notification').is(":checked") && currentForm.find('#sms').is(":checked"))) {
                currentForm.find('#push-text-div').show();
                //deep link screen disable for merchant offer
                currentForm.find('#deep_link_screen').prop('disabled', false);
                currentForm.find("input:radio[name='message_type']").each(function () {
                    if ($(this).parent().hasClass('checked')) {

                        currentForm.find('#deep_link_screen').prop('disabled', false);
                        currentForm.find('#deep_link_screen').select2('val', '');


                    }
                });
                currentForm.find('#sms-text-div').show();
                currentForm.find('#push_notification').attr("disabled", true);
                currentForm.find('#sms').attr("disabled", true);
            } else {
                currentForm.find('#push-text-div').hide();
                currentForm.find('#sms-text-div').hide();
                currentForm.find('#test_mobile_numbers').val('');
                currentForm.find('#push_notification').attr("disabled", false);
                currentForm.find('#sms').attr("disabled", false);
            }
            //send test email sms btn enable disable conditions                        
            if ($(this).is(":checked") || currentForm.find('#sms').is(":checked")) {
                currentForm.find('#send-sms-btn').prop('disabled', false);
                if (currentForm.find('#email').is(":checked")) {
                    currentForm.find('#send-email-btn').prop('disabled', false);
                } else {
                    currentForm.find('#send-email-btn').prop('disabled', true);
                }
            } else {
                currentForm.find('#send-sms-btn').prop('disabled', true);
            }
        });
    };

    var sendTodayDiv = function (currentForm) {

        $('body').on('click', '#send_today', function (e) {

            if ($(this).is(":checked")) {

                $('#' + currentForm).find("#row-message-send-date-div").hide();
                if (currentForm !== 'edit-communication') {
                    $('#' + currentForm).find("#message_send_time").val('');
                }
                $('#' + currentForm).find("#date-picker-btn").attr('disabled', 'disabled');
                $('#' + currentForm).find("#message_send_time").prop('disabled', true);
                $('#' + currentForm).find("#send_today_message").html('<span class="label label-danger span_send_today_message">NOTE! </span><span class="span_send_today_message" style="margin-left:10px;">You cannot edit this Message/Offer after submission.</span>');
                $('#' + currentForm).find('#message-send-date-div .form-group').removeClass('has-error');
                $('#' + currentForm).find('#message_send_time-error').remove();
                //
            } else {
                $('#' + currentForm).find("#row-message-send-date-div").show();
                $('#' + currentForm).find("#date-picker-btn").attr('disabled', false);
                $('#' + currentForm).find("#message_send_time").prop('disabled', false);
                $('#' + currentForm).find("#send_today_message").html('');
            }
        });
    };

    var handleTimePickers = function () {

        if (jQuery().timepicker) {
            var d = new Date();
            var h = d.getHours() + 1;
            var m = d.getMinutes();
            var ampm = '';
            if (h > 12) {
                h = h - 12;
                ampm = 'PM'
            } else {
                ampm = 'AM'
            }
            $('#today_time').timepicker({
                autoclose: true,
                showSeconds: false,
                snapToStep: true,
                minuteStep: 15,
                defaultTime: h + ':' + m + ' ' + ampm
            });

            $('.timepicker').parent('.input-group').on('click', '.input-group-btn', function (e) {
                e.preventDefault();
                $(this).parent('.input-group').find('.timepicker').timepicker('showWidget');
            });
        }
    };

    var sendTestSms = function (currentForm) {
        $('body').on('click', '.send-sms', function (e) {
            var actionUrl = adminUrl + '/communications/send-test-sms';

            if ($('#' + currentForm).find('#test_mobile_numbers').val() == '' && $('#' + currentForm).find('#sms_text').val() == '') {
                var error = 'Please enter Message Text and Mobile Number.';
                Metronic.alert({
                    type: 'danger',
                    icon: 'times',
                    message: error,
                    container: $('#ajax-response-text'),
                    place: 'prepend',
                    closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                });
                return false;
            }

            var formData = new FormData();
            formData.append('merchant_id', $('#' + currentForm).find('#merchant_id').val());
            formData.append('test_mobile_numbers', $('#' + currentForm).find('#test_mobile_numbers').val());
            formData.append('sms_text', $('#' + currentForm).find('#sms_text').val());

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (data) {
                    //$('#' + currentForm).find('#test_mobile_numbers').val('');
                    if (data.status == 'success') {
                        Metronic.alert({
                            type: 'success',
                            icon: 'check',
                            message: data.message,
                            container: $('#ajax-response-text'),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    } else {
                        Metronic.alert({
                            type: 'danger',
                            icon: 'times',
                            message: data.message,
                            container: $('#ajax-response-text'),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    }

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
        });
    };

    var sendTestEmail = function (currentForm) {
        $('body').on('click', '.send-email', function (e) {
            var actionUrl = adminUrl + '/communications/send-test-email';

            if ($('#' + currentForm).find('#test_email_addresses').val() == '' && $('#' + currentForm).find('#email_from_name').val() == '' && $('#' + currentForm).find('#email_from_email').val() == '' && $('#' + currentForm).find('#email_subject').val() == '') {
                var error = 'Please enter Send Email Data.';
                Metronic.alert({
                    type: 'danger',
                    icon: 'times',
                    message: error,
                    container: $('#ajax-response-text'),
                    place: 'prepend',
                    closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                });
                return false;
            }

            var formData = new FormData();
            formData.append('merchant_id', $('#' + currentForm).find('#merchant_id').val());
            formData.append('test_email_addresses', $('#' + currentForm).find('#test_email_addresses').val());
            formData.append('email_from_name', $('#' + currentForm).find('#email_from_name').val());
            formData.append('email_from_email', $('#' + currentForm).find('#email_from_email').val());
            formData.append('email_subject', $('#' + currentForm).find('#email_subject').val());
            var emailBody = CKEDITOR.instances.email_body.getData()
            formData.append('email_body', emailBody);
            formData.append('email_tags', $('#' + currentForm).find('#email_tags').val());

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                "headers": { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (data) {
                    //$('#' + currentForm).find('#test_email_addresses').val('');
                    if (data.status == 'success') {
                        Metronic.alert({
                            type: 'success',
                            icon: 'check',
                            message: data.message,
                            container: $('#ajax-response-text'),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    } else {
                        Metronic.alert({
                            type: 'danger',
                            icon: 'times',
                            message: data.message,
                            container: $('#ajax-response-text'),
                            place: 'prepend',
                            closeInSeconds: siteObjJs.admin.commonJs.constants.alertCloseSec
                        });
                    }

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
        });
    };

    $.validator.addMethod('validWebUrl', function (value) {
        var url_validate = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
        if (!url_validate.test(value) && value != '') {
            return false;
        } else {
            return true;
        }
    }, 'Please enter Valid URL.');

    $.validator.addMethod("validEmail", function (value, element) {
        return this.optional(element) || /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/.test(value);
    }, "Please enter a valid Email Address.");

    $.validator.addMethod('validPushText', function (value, element) {
        var formElement = $(element).closest("form");
        var formId = formElement.attr("id");
        if ($('#' + formId).find('#push_notification').is(':checked') || $('#' + formId).find('#sms_notification').is(':checked')) {
            if ($.trim(value) == '') {
                return false;
            } else {
                return true;
            }
        }

    }, 'Please enter Notification Text.');

    $.validator.addMethod('validDeepLinkScreen', function (value, element) {
        var formElement = $(element).closest("form");
        var formId = formElement.attr("id");
        if ($('#' + formId).find('#push_notification').is(':checked') || $('#' + formId).find('#sms_notification').is(':checked')) {
            if ($.trim(value) == '') {
                return false;
            } else {
                return true;
            }
        }

    }, 'Please select Deep Link Screen.');

    $.validator.addMethod('validSmsText', function (value, element) {
        var formElement = $(element).closest("form");
        var formId = formElement.attr("id");
        if ($('#' + formId).find('#sms').is(':checked') || $('#' + formId).find('#sms_notification').is(':checked')) {
            if ($.trim(value) == '') {
                return false;
            } else {
                return true;
            }
        }

    }, 'Please enter Message Text.');

    $.validator.addMethod('validEmailFromName', function (value, element) {
        var formElement = $(element).closest("form");
        var formId = formElement.attr("id");
        if ($('#' + formId).find('#email').is(':checked')) {
            if ($.trim(value) == '') {
                return false;
            } else {
                return true;
            }
        }
    }, 'Please enter Sender Name.');

    $.validator.addMethod('validEmailFromEmail', function (value, element) {
        var formElement = $(element).closest("form");
        var formId = formElement.attr("id");
        if ($('#' + formId).find('#email').is(':checked')) {
            if ($.trim(value) == '') {
                return false;
            } else {
                return true;
            }
        }
    }, 'Please enter Sender Email.');

    $.validator.addMethod('validEmailSubject', function (value, element) {
        var formElement = $(element).closest("form");
        var formId = formElement.attr("id");
        if ($('#' + formId).find('#email').is(':checked')) {
            if ($.trim(value) == '') {
                return false;
            } else {
                return true;
            }
        }
    }, 'Please enter Email Subject.');

    $.validator.addMethod('validEmailBody', function (value, element) {
        var formElement = $(element).closest("form");
        var formId = formElement.attr("id");
        if ($('#' + formId).find('#email').is(':checked')) {
            if (CKEDITOR.instances.editor1.updateElement()) {
                return false;
            } else {
                return true;
            }
        }
    }, 'Please enter Email Body.');


    var checkPastTime = function (obj) {


        $('body').on('change', '#today_time', function (e) {

            var currentFormObj = $('#' + obj);

            if (currentFormObj.find('#send_today').parent('span').hasClass('checked')) {
                var todayTime = $(this).val();

                var actionUrl = adminUrl + '/communications/check-past-time/' + todayTime;
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    dataType: "json",
                    type: "GET",
                    success: function (data) {
                        currentFormObj.find('#today_time-error').html('');
                        if (data.status == 'error') {
                            currentFormObj.find('#today_time-error').html('Send Notification Time should be greater than current time.');
                            $("form").submit(function (e) {
                                e.preventDefault();
                            });
                            return false;
                        } else {
                            currentFormObj.find('#today_time-error').html('');
                            return true;
                        }

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
        });
    }







    return {
        //main function to initiate the module
        init: function (obj) {

            initializeListener();
            // handleTable();
            fetchDataForEdit(obj);
            handleBootstrapMaxlength();
            //getMerchantLoyaltyProgram(obj);
            // getMerchantLoyaltyOfferData(obj);
            handleDatePicker();
            enableTestingModeDiv(obj);
            showEmailFormDiv(obj);
            getUserTypeData(obj);
            showNotificationsFormDiv(obj);
            showSmsFormDiv(obj);
            showRegionFormDiv(obj);
            showUserFormDiv(obj);
            handleTimePickers();
            sendTodayDiv(obj);
            showSmsAndNotificationsFormDiv(obj);
            sendTestSms(obj);
            sendTestEmail(obj);
            checkPastTime(obj);
            // changeMessageType(obj);
            if (obj === 'create-communication') {
                userDataCommon(obj);
            }
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#' + obj, handleAjaxRequest);
            $('#' + obj).find('input:checkbox').uniform();
            $('#' + obj).find('input:radio').uniform();

        }

    };
}();