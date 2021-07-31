/**
 * @file js.validation.js
 *
 * @version
 *
 * @package Javascript
 * @author Nilesh G. Pangul
 * @copyright (c) 2015-2016 iProgrammer Solutions Private Limited
 * @license https://www.iprogrammer.com/privacy-policy/
 */

/**
 * Validation javascript prototype class definition
 *
 * @class validation
 * @see
 * @todo
 */

siteObjJs.validation = function () {
    // basic validation
    function formValidateInit(formIdentifier, submitHandler, rules, messages) {
        var formIdentity = $(formIdentifier);
        var errorMsg = $('.alert-danger', formIdentity);
        var successMsg = $('.alert-success', formIdentity);
        var errorExtra = $('.extraErrorContainer', formIdentity);
        rules = rules || {};
        messages = messages || {};

        formIdentity.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: ':hidden', // validate all fields including form hidden input
            messages: messages,
            rules: rules,
            submitHandler:  submitHandler,
            invalidHandler: function (event, validator) { //display error alert on form submit        
                errorExtra.show();
                successMsg.hide();
                errorMsg.show();
                Metronic.scrollTo(errorMsg, -200);
            },
            errorPlacement: errorPlacement,
            highlight: function (element) { // hightlight error inputs                
                $(element)
                        .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group   
            },
            unhighlight: function (element) { // revert the change done by hightlight
            },
            success: function (label, element) {
                errorExtra.hide();
                var icon = $(element).parent('.input-icon').children('i');
                $(element).closest('.form-group').removeClass('has-error');//.addClass('has-success'); // set success class to the control group
                icon.removeClass('fa-warning').addClass('fa-check');
            }
        });

        $('.select2me', formIdentity).change(function () {
            formIdentity.validate().element($(this));
        });
    }

    function errorPlacement(error, element) { // render error placement for each input type
        if (element.parents('.input-medium-suffix').length > 0) {            
            error.appendTo(element.parents('.input-medium-suffix').attr('data-error-container'));
            
        } else if (element.parent('.input-group').length > 0) {
            error.insertAfter(element.parent('.input-group'));
            
        } else if (element.attr('data-error-container')) {
            error.appendTo(element.attr('data-error-container'));
            
        } else if (element.parents('.radio-list').length > 0) {
            error.appendTo(element.parents('.radio-list').attr('data-error-container'));
           
        } else if (element.parents('.radio-inline').length > 0) {
            error.appendTo(element.parents('.radio-inline').attr('data-error-container'));
            
        } else if (element.parents('.checkbox-list').length > 0) {
            error.appendTo(element.parents('.checkbox-list').attr('data-error-container'));
            
        } else if (element.parents('.checkbox-inline').length > 0) {
            error.appendTo(element.parents('.checkbox-inline').attr('data-error-container'));
           
        } else {
            error.insertAfter(element); // for other inputs, just perform default behavior
            
        }
        
    }

    return {
        'formValidateInit': formValidateInit,
        'errorPlacement': errorPlacement
    };

}();
