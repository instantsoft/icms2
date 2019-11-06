toastr.options = {progressBar: true, preventDuplicates: true};
var icms = icms || {};
icms.admin = (function ($) {

    this.onDocumentReady = function(){

        $('.need-scrollbar').each(function (){
            new PerfectScrollbar('#'+$(this).attr('id'));
        });

        $('.form-tabs').on('focus', '.field.ft_string > input, .field.ft_text > textarea', function (){
            $('.pattern_fields_panel').hide();
            $('.pattern_fields_panel_hint').show();
            $(this).closest('.field').find('.pattern_fields_panel_hint').hide();
            $(this).closest('.field').find('.pattern_fields_panel').show();
        });
        $('.form-tabs').on('click', '.pattern_fields > ', function (){
            var spacer = $(this).closest('.hint').data('spacer') || false;
            var spacer_stop = $(this).closest('.hint').data('spacer_stop') || false;
            return addTextToPosition($(this).closest('.field').find('input, textarea'), $(this).text(), spacer, spacer_stop);
        });

        $('.auto_copy_value').on('click', function (){
            $(this).closest('.input-prefix-suffix').find('input').val($(this).data('value'));
            return false;
        });

        $('.sidebar-minimizer').on('click', function (){
            var current = $(this).data('current_state');
            if(current == 1){
                var new_state = 0;
            } else {
                var new_state = 1;
            }
            $(this).data('current_state', new_state);
            $.cookie('icms[hide_sidebar]', new_state, { expires: 30, path: '/'});
        });

        $('.sidebar-toggler').on('click', function (){
            var current = $(this).data('current_state');
            if(current == 1){
                var new_state = 0;
            } else {
                var new_state = 1;
            }
            $(this).data('current_state', new_state);
            $.cookie('icms[close_sidebar]', new_state, { expires: 30, path: '/'});
        });

        var quickview_ps = null;

        $('[data-toggle="quickview"]').on('click', function (e){
            e.preventDefault();
            var quickview = $(this).data('toggle-element');
            var is_open = $(quickview).hasClass('open');
            if(is_open){
                if(quickview_ps !== null){
                    quickview_ps.destroy();
                }
                $('body').removeClass('quickview-wrapper-show');
                $(quickview).removeClass('open no-overflow');
            } else {
                quickview_ps = new PerfectScrollbar(quickview);
                $('body').addClass('quickview-wrapper-show');
                $(quickview).addClass('open no-overflow');
            }
            return false;
        });

        $('.custom-file-input').on('change',function(){
            $(this).next('.custom-file-label').html($(this).val().replace('C:\\fakepath\\', ''));
        });

        if($('#breadcrumb .breadcrumb-item').length > 4){

            var prev_href = $('.breadcrumb-item.active').prev().find('a').attr('href');

            $('#admin_toolbar a.nav-link[href="'+prev_href+'"]').addClass('active');

        }

    };

    this.dbCardSpinner = function (el){
        return $(el).closest('.card').find('.db_spinner');
    };

    this.goToLinkAnimated = function (link){

        link = $(link);

        $(link).prop('disabled', true);
        $(link).html('<div class="spinner mr-2"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

        window.location.href = link.attr('href');

        return false;

    };

    return this;

}).call(icms.admin || {},jQuery);