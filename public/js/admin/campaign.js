siteObjJs.admin.campaignJs = function () {

    // Initialize all the page-specific event listeners here.

    var initializeListener = function (formId) {

        $('body').on('change', "input:radio[name='target_customer']", function (e) {
            var url = adminUrl + "/campaigns/getAllActiveCustomer";
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    var $ell = $('#' + formId).find("#target_customer_value");
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

            if ($(this).val() === '2') {
                $('#' + formId).find('#user-selection-div').show();
            } else {
                $('#' + formId).find('#user-selection-div').hide();
            }
        });

        $('body').on('change', 'select[name="campaign_category_id"]', function (e) {
            var categoryId = $(this).val();
            var url = adminUrl + "/campaigns/getCampaignMaster";
            url = url + '/' + categoryId;
            if (categoryId) {
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        if (data.is_campaign_master_available > 0) {
                            var $ell = $('#' + formId).find("#campaign_master_id");
                            $ell.empty();
                            $ell.select2("val", '');
                            $ell.empty(); // remove old options  

                            $.each(data.campaign_master, function (value, key) {
                                $ell.append($('<option>', {
                                    value: value,
                                    text: key,
                                }));
                            });

                        } else {
                            $("#" + formId + " #campaign_category_id option:selected").prop("selected", false)
                            //  $("#select2-category_id-container").text('');
                            alert('No sub category is available');
                        }
                    }
                });
            } else {
                $('#' + formId).find("#campaign_master_id").empty();
            }
        });

    };

    $.validator.addMethod('startDateValid', function (value, element) {

        var formElement = $(element).closest("form");
        var formId = formElement.attr("id");
        
        if(value == '' ){
            return false;
        }
        $('#' + formId).find('#end_date').attr('min',value);
        return true;

    }, 'Please select valid Start Date.');

    $.validator.addMethod('endDateValid', function (value, element) {
        var formElement = $(element).closest("form");
        var formId = formElement.attr("id");
        var startDate = $('#' + formId).find('#start_date').val();

        if(value == '' || startDate == ''){
            return false;
        }
        var endDate=new Date(value);
        var startDate = new Date(startDate);
        if(startDate > endDate){
            return false;
        }
        return true;

    }, 'Please select valid End Date.');

    // Method to fetch and place edit form with data using ajax call
    var fetchDataForEdit = function (obj) {
        if (obj === 'update_campaign') {
            var userTypeFlag = $('#' + obj).find('input[name="target_customer"]:checked').val();
            if (userTypeFlag === '2') {
                $('#' + obj).find('#user-selection-div').show();
            } else {
                $('#' + obj).find('#user-selection-div').hide();
            }
        }
    };
    // Common method to handle add and edit ajax request and reponse
    // method to handle add ajax request and reponse
    var handleAjaxRequest = function (form, e) {

        var currentForm1 = $(form);


        form.submit();
        //$('ol').append('<li>' + $(form[0]).val() + '</li>');
        // validator = form.validate();
        // validator.resetForm();
        // validator.showErrors({ field: 'Validation failed'} );
        // console.log('inside handler');
    };

    var handleTable = function () {

        grid = new Datatable();
        grid.init({
            src: $('#merchant-campaign-table'),
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
                    { data: null, name: 'rownum', searchable: false },
                    { data: 'id', name: 'id', visible: false },
                    { data: 'merchant_name', name: 'merchant_name' },
                    { data: 'location_name', name: 'location_name' },
                    { data: 'brand_name', name: 'brand_name' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at', visible: false },
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
                    api.column(8, { page: 'current' }).data().each(function (group, i) {
                        var status = $(rows).eq(i).children('td:nth-child(9)').html();
                        var statusBtn = '';
                        if (status == 1) {
                            statusBtn = '<span class="label label-sm label-success">active</span>';
                        } else {
                            statusBtn = '<span class="label label-sm label-danger">inactive</span>';
                        }
                        $(rows).eq(i).children('td:nth-child(9)').html(statusBtn);
                    });
                },
                "ajax": {
                    "url": "merchant-campaign/data",
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



    return {
        //main function to initiate the module
        init: function (obj) {
            initializeListener(obj);
            // handleTable();
            fetchDataForEdit(obj);                          
            //bind the validation method to 'add' form on load
            siteObjJs.validation.formValidateInit('#' + obj, handleAjaxRequest);

        }

    };
}();