siteObjJs.admin.unitJs = function () {

    // Initialize all the page-specific event listeners here.
    
        var initializeListener = function () {
            $('body').on("click", ".btn-collapse", function () {
                $("#ajax-response-text").html("");
                //retrieve id of form element and create new instance of validator to clear the error messages if any
                var formElement = $(this).closest("form");
                var formId = formElement.attr("id");
                var validator = $('#' + formId).validate();
                validator.resetForm();
                //remove any success or error classes on any form, to reset the label and helper colors
                $('.form-group').removeClass('has-error');
                $('.form-group').removeClass('has-success');
            });
    
        };
        // Method to fetch and place edit form with data using ajax call
    
        var fetchDataForEdit = function () {
            $('.portlet-body').on('click', '.edit-form-link', function () {
                var cat_id = $(this).attr("id");
                var actionUrl = 'units-master/' + cat_id + '/edit';
                $.ajax({
                    url: actionUrl,
                    cache: false,
                    dataType: "json",
                    type: "GET",
                    success: function (data)
                    {
                        $("#edit_form").html(data.form);
                        $('form').find('input:radio').uniform();
                        handleBootstrapMaxlength();
                        siteObjJs.validation.formValidateInit('#edit-unit-master', handleAjaxRequest);
                    },
                    error: function (jqXhr, json, errorThrown)
                    {
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
            var formElement = $(this.currentForm); // Retrive form from DOM and convert it to jquery object
            var actionUrl = formElement.attr("action");
            var actionType = formElement.attr("method");
            var formData = formElement.serialize();
            var icon = "check";
            var messageType = "success";
            $.ajax(
                    {
                        url: actionUrl,
                        cache: false,
                        type: actionType,
                        data: formData,
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
    
        var handleTable = function () {
    
            grid = new Datatable();
            grid.init({
                src: $('#unit-master-table'),
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
                        {data: null, name: 'rownum', searchable: false},
                        {data: 'id', name: 'id', visible: false},
                        {data: 'cat_name', name: 'cat_name'},
                        {data: 'cat_description', name: 'cat_description'},
                        {data: 'status', name: 'status'},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    "drawCallback": function (settings) {
                        var api = this.api();
                        var rows = api.rows({page: 'current'}).nodes();
                        var last = null;
                        var page = api.page();
                        var recNum = null;
                        var displayLength = settings._iDisplayLength;
                        api.column(0, {page: 'current'}).data().each(function (group, i) {
                            recNum = ((page * displayLength) + i + 1);
                            $(rows).eq(i).children('td:first-child').html(recNum);
                        });
                        api.column(3, {page: 'current'}).data().each(function (group, i) {
                            var status = $(rows).eq(i).children('td:nth-child(4)').html();
                            var statusBtn = '';
                            if (status == 1) {
                                statusBtn = '<span class="label label-sm label-success">active</span>';
                            } else {
                                statusBtn = '<span class="label label-sm label-danger">inactive</span>';
                            }
                            $(rows).eq(i).children('td:nth-child(4)').html(statusBtn);
                        });
                    },
                    "ajax": {
                        "url": "units-master/data",
                        "type": "GET"
                    },
                    "order": [
                        [2, "asc"]
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
    
        var handleBootstrapMaxlength = function () {
            $('#create-unit-master').find("textarea").maxlength({
                limitReachedClass: "label label-danger",
                alwaysShow: true,
                placement: 'bottom-right',
                threshold: 10
            });
            $('#edit-unit-master').find("textarea").maxlength({
                limitReachedClass: "label label-danger",
                alwaysShow: true,
                placement: 'bottom-right',
                threshold: 10
            });
        };
    
        return {
            //main function to initiate the module
            init: function () {
                initializeListener();
                handleTable();
                fetchDataForEdit();
                handleBootstrapMaxlength();
                //bind the validation method to 'add' form on load
                siteObjJs.validation.formValidateInit('#create-unit-master', handleAjaxRequest);
            }
    
        };
    }();