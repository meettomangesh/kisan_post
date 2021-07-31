siteObjJs.admin.commonJs = function () {

    $( document ).ajaxComplete(function( event,request, settings ) {
        $('form').find('input:checkbox').uniform();
    });
        
    var constants = {
        alertCloseSec: 10,
        recordsPerPage: 20,
        gridLengthMenu: [
            [10, 20, 50, 100, 150, 200, 500],
            [10, 20, 50, 100, 150, 200, 500]
        ]
    };

    var boxExpandBtnClick = function () {
        $('body').on('click', '.btn-expand-form', function () {
            $('.edit-form-main').hide();
            $('.add-form-main').show();
            $('.expand.box-expand-form').trigger('click');
            $('html, body').animate({scrollTop: 10}, 500);
            $('.add-form-main form :input:visible:enabled:first').focus();
        });
        $('body').on('click', '.btn-collapse-form', function () {
            $(this).closest('form').trigger('reset');
            $('.collapse.box-expand-form').trigger('click');
        });
        $('body').on('click', '.btn-collapse-form-edit', function () {
            $(this).closest('form').trigger('reset');
            $('.edit-form-main').hide();
            $('.add-form-main').show();
            $('.collapse.box-expand-form').trigger('click');
            $('html, body').animate({scrollTop: 10}, 500);
            $('.add-form-main form :input:visible:enabled:first').focus();
        });
        $('.portlet-body').on('click', '.edit-form-link', function () {
            $('.edit-form-main').show();
            $('.add-form-main').hide();
            $('.expand.box-expand-form').trigger('click');
            $('html, body').animate({scrollTop: 10}, 500);
            $('.edit-form-main form :input:visible:enabled:first').focus();
        });
        $('.portlet-body').on('click', '.trash-form-link', function () {
            bootbox.confirm({
                buttons: {confirm: {label: 'CONFIRM'}},
                message: 'Are you sure you want to delete this record?',
                callback: function (result) {
                    //action will perform here
                }
            });
        });

        $('.togglelable').click(function (e) {
            e.stopPropagation();
            var titleAnchor = $(this).find(".tools > a.box-expand-form");
            if (titleAnchor.hasClass('expand')) {
                titleAnchor.addClass('collapse');
                titleAnchor.removeClass('expand');
            }
            else if (titleAnchor.hasClass('collapse')) {
                titleAnchor.addClass('expand');
                titleAnchor.removeClass('collapse');
            }
            $(this).parent().find(".portlet-body").toggle();
            $(this).parent().find('.portlet-body form :input:visible:enabled:first').focus();

        });
    };

    var expandCollapseMenu = function () {
        $('.menu-expand-all').click(function () {
            $('.sub-menu').css({'display': 'block'});
            $('.arrow').addClass('open');
        });
        $('.menu-collapse-all').click(function () {
            $('.sub-menu').css({'display': 'none'});
            $('.arrow').removeClass('open');
        });

        $('.sidebar-toggler').click(function () {
            var body = $('body');
            var menuExpandCollapseBtn = $('.menuExpandCollapseBtn');
            if (body.hasClass("page-sidebar-closed")) {
                menuExpandCollapseBtn.removeClass('hidden');
            } else {
                menuExpandCollapseBtn.addClass('hidden');
            }
        });
    };

    var showSelectedMenus = function () {
        var submenuName = $("#submenu_name").val();
        var menuName = $("#menu_name").val();
        $('#' + menuName + ' .arrow').addClass('open');
        $('#' + menuName + ' .sub-menu').addClass("showBlock");
        $('#' + menuName + '>a').addClass("showSelected");
        $('#' + submenuName).addClass("showSelected");

        if (typeof menuName == 'undefined') {
            $('#Dashboard').addClass("showSelected");
        }
        ;
    };

    return {
        'boxExpandBtnClick': boxExpandBtnClick,
        'expandCollapseMenu': expandCollapseMenu,
        'showSelectedMenus': showSelectedMenus,
        'constants': constants
    };

}();