var FormWizard = function () {


    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

            function format(state) {
                if (!state.id)
                    return state.text; // optgroup
                return "<img class='flag' src='../../assets/global/img/flags/" + state.id.toLowerCase() + ".png'/>&nbsp;&nbsp;" + state.text;
            }

            $("#country_list").select2({
                placeholder: "Select",
                allowClear: true,
                formatResult: format,
                width: 'auto',
                formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });

            var form = $('#create-loyalty-program');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            console.log('===form wizard 1')            
             $.validator.addMethod("membershipStartNumberLength", function (value, element)
            {
                var membershipIdLength = $('.portlet-body').find('#membership_id_length').val();               
                if (value.length == membershipIdLength) {
                    return true;
                } else {
                    return false;
                }
                ;
            }, 'Please enter number of digits equal to selected Membership ID Length.');
            
            form.validate({
             doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
             errorElement: 'span', //default input error message container
             errorClass: 'help-block help-block-error', // default input error message class
             focusInvalid: false, // do not focus the last invalid input
             /*rules: {
                membership_start_number: {
                     membershipStartNumberLength:true
                 }
             },*/
            /* rules: {
                //basic loyalty details
                loyalty_program_name: {
                   required: true,
                   minlength: 2,
                   maxlength: 50                
                },
                loyalty_program_short_name: {
                   required: true,
                   minlength: 2,
                   maxlength: 50,                
                },
                engine_type: {
                    required: true,
                    minlength: 1
                },
                online_shop_url: {
                   maxlength: 255,
                   required: true
                },
                forgot_password_url: {
                   maxlength: 255,
                   required: true
                },
                tier_count: {
                   required: true,
                   minlength: 1
                }             
             },*/
             
             
             
            messages: { // custom messages for radio buttons and checkboxes
                                   
             },
             
             errorPlacement: function (error, element) { // render error placement for each input type
             if (element.attr("name") == "min_order_amount_toshowdetails") {            
                error.insertAfter("#form_min_order_amount_toshowdetails_error");
             } else if (element.attr("name") == "minimum_points_to_show_transaction_details") {            
                error.insertAfter("#form_minimum_points_to_show_transaction_details_error");
             } else if (element.attr("name") == "min_points_available_to_redeem") {            
                error.insertAfter("#form_min_points_available_to_redeem_error");
             } else if (element.attr("name") == "max_points_redeem_at_a_time") {            
                error.insertAfter("#form_max_points_redeem_at_a_time_error");
             } else {
             error.insertAfter(element); // for other inputs, just perform default behavior
             }
             },
             
             invalidHandler: function (event, validator) { //display error alert on form submit            
             success.hide();
             error.show();
             //App.scrollTo(error, -200);
             },
             
             highlight: function (element) { // hightlight error inputs         
             $(element)
             .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
             },
             
             unhighlight: function (element) { // revert the change done by hightlight         
             $(element)
             .closest('.form-group').removeClass('has-error'); // set error class to the control group
             },
             
             success: function (label) {                 
             if (label.attr("for") == "gender" || label.attr("for") == "payment[]") { // for checkboxes and radio buttons, no need to show OK icon
             label
             .closest('.form-group').removeClass('has-error').addClass('has-success');
             label.remove(); // remove error label here
             } else { // display success icon for other inputs
             label
             .addClass('valid') // mark the current input as valid and display OK icon
             .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
             }
             },
             
             submitHandler: function (form) {            
             success.show();
             error.hide();
             form[0].submit();
             //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
             }
             
             });

            var displayConfirm = function () {                
                $('#tab4 .form-control-static', form).each(function () {
                    var input = $('[name="' + $(this).attr("data-display") + '"]', form);
                    if (input.is(":radio")) {
                        input = $('[name="' + $(this).attr("data-display") + '"]:checked', form);
                    }
                    if (input.is(":text") || input.is("textarea")) {
                        $(this).html(input.val());
                    } else if (input.is("select")) {
                        $(this).html(input.find('option:selected').text());
                    } else if (input.is(":radio") && input.is(":checked")) {
                        $(this).html(input.attr("data-title"));
                    } else if ($(this).attr("data-display") == 'payment[]') {
                        var payment = [];
                        $('[name="payment[]"]:checked', form).each(function () {
                            payment.push($(this).attr('data-title'));
                        });
                        $(this).html(payment.join("<br>"));
                    }
                });
            }

            var handleTitle = function (tab, navigation, index) {                
                var total = navigation.find('li').length;
                var current = index + 1;
                // set wizard title
                $('.step-title', $('#form_wizard_1')).text('Step ' + (index + 1) + ' of ' + total);
                // set done steps
                jQuery('li', $('#form_wizard_1')).removeClass("done");
                var li_list = navigation.find('li');
                for (var i = 0; i < index; i++) {
                    jQuery(li_list[i]).addClass("done");
                    $('#form_wizard_1').find('li.done > a.step .number').css('background-color','#26a69a');  //completed step number bg color as green                  
                    $('#form_wizard_1').find('li.done > a.step .number').css('color','#fff');  //completed step number bg color as white                  
                }

                if (current == 1) {
                    $('#form_wizard_1').find('.button-previous').hide();
                } else {
                    $('#form_wizard_1').find('.button-previous').show();
                }

                if (current >= total) {
                    $('#form_wizard_1').find('.button-next').hide();
                    $('#form_wizard_1').find('.button-submit').show();
                    displayConfirm();
                } else {
                    $('#form_wizard_1').find('.button-next').show();
                    $('#form_wizard_1').find('.button-submit').hide();
                }
                //App.scrollTo($('.page-title'));
            }

            // default form wizard

            $('#form_wizard_1').bootstrapWizard({
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                onTabClick: function (tab, navigation, index, clickedIndex) {
                    return false;

                    success.hide();
                    error.hide();
                    if (form.valid() == false) {
                        return false;
                    }
                    handleTitle(tab, navigation, clickedIndex);
                },
                onNext: function (tab, navigation, index) {
                    success.hide();
                    error.hide();                    
                    //form.validate();
                    if (form.valid() == false) {                        
                        return false;
                    }

                    handleTitle(tab, navigation, index);
                },
                onPrevious: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    handleTitle(tab, navigation, index);
                },
                onTabShow: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var percent = (current / total) * 100;
                    $('#form_wizard_1').find('.progress-bar').css({
                        width: percent + '%'
                    });
                }
            });
            siteObjJs.validation.formValidateInit('#create-loyalty-program', function () {
                $('#form_wizard_1').bootstrapWizard('next');
            });
            $('#form_wizard_1').find('.button-previous').hide();
            $('#form_wizard_1 .button-submit').click(function () {
                alert('Finished! Hope you like it :)');
            }).hide();
           }

    };

}();

jQuery(document).ready(function () {
    FormWizard.init();
});