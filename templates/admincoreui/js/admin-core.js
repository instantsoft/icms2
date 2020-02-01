toastr.options = {progressBar: true, preventDuplicates: true, timeOut: 3000, newestOnTop: true, closeButton: true, hideDuration: 600};
var icms = icms || {};
icms.admin = (function ($) {

    this.onDocumentReady = function(){

        toolbarScroll.init();

        $('.cp_toolbar').on('click', 'a.scroll_top', function(event){
            $('body,html').animate({
                scrollTop: 0 ,
            }, 200);
            return false;
        });

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

icms.notices = (function ($) {

    this.onDocumentReady = function(){

        $('#notices_counter').on('click',function(e){
            e.preventDefault();
            $.post($(this).attr('href'), {}, function(html){
                $('#pm_notices_list').html(html);
            });
        });
        $('#pm_notices_list').on('click', function (e) {
            e.stopPropagation();
        });

    };

    this.noticeAction = function(id, name){

        var pm_notices_window = $('#pm_notices_list');

        var url = $('#pm_notices_window').data('action-url');

        var form_data = {
            notice_id: id,
            action_name: name
        };

        $.post(url, form_data, function(result) {

            if (result.error) {
                return false;
            }

            if (result.href){
                window.location.href = result.href;
            }

            $('#notice-'+id, pm_notices_window).fadeOut(300, function(){
                $(this).remove();
                var count = $('.item', pm_notices_window).length;
                icms.notices.setNoticesCounter(count);
                if (count==0){
                    $('body').trigger('click');
                }
            });

        }, "json");

        return false;

    };

    this.noticeClear = function(){

        if(confirm(LANG_PM_CLEAR_NOTICE_CONFIRM)){

            var pm_notices_window = $('#pm_notices_list');
            var url = $('#pm_notices_window').data('action-url');

            $.post(url, {action_name: 'clear_notice'}, function(result) {

                if (result.error) {
                    return false;
                }

                $('.item', pm_notices_window).fadeOut('fast', function(){
                    $(this).remove();
                    var count = $('.item', pm_notices_window).length;
                    icms.notices.setNoticesCounter(count);
                    if (count==0){
                        $('body').trigger('click');
                    }
                });

            }, 'json');

            return true;

        }

        return false;

    };

    this.setNoticesCounter = function(value){

        var button = $('#notices_counter');

        $('.badge', button).remove();

        if (value > 0){
            var html = '<span class="badge badge-pill badge-danger">' + value + '</span>';
            $(button).append(html);
        }

    };

	return this;

}).call(icms.notices || {},jQuery);
toolbarScroll = {
    win: null,
    toolbar: null,
    spacer: null,
    spacer_init: false,
    offset: 0,
    init: function (){
        this.win     = $(window);
        this.toolbar = $('.cp_toolbar');
        if(this.toolbar.length == 0){
            return;
        }
        this.offset  = (this.toolbar).offset().top-55;
        if(this.spacer_init === false){
            this.spacer_init = true;
            $(this.toolbar).after($('<div id="fixed_toolbar_spacer" />').hide());
            this.spacer = $('#fixed_toolbar_spacer');
            $(this.toolbar).append('<a class="btn btn-success scroll_top" href="#"><i class="icon-arrow-up-circle icons font-2xl"></i></a>');
        }
        this.run();
    },
    run: function (){
        var handler = function (){
            toolbarScroll.doAutoScroll();
        };
        this.win.off('scroll', handler).on('scroll', handler).trigger('scroll');
    },
    doAutoScroll: function (){
        var scroll_top = this.win.scrollTop();
        if (scroll_top > this.offset) {
            if(!$(this.toolbar).hasClass('fixed_toolbar')){
                $(this.toolbar).addClass('fixed_toolbar');
                $(this.spacer).show();
            }
        } else {
            $(this.toolbar).removeClass('fixed_toolbar');
            $(this.spacer).hide();
        }
    }
};