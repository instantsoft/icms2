var fit_layout_delta = 0;
var hh1, hh2;
function fitLayout(){
    if(!hh1 && !hh2){
        return;
    }
    $('table.layout').height(hh2 - hh1 - 2 + fit_layout_delta);
    $('table.layout').width( $('#cp_body').width() + 40 );
}
var toolbarScroll = {
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
        this.offset  = (this.toolbar).offset().top;
        if((+$('#wrapper').height() - +$(this.win).height()) <= (this.offset + 20)){
            return;
        }
        if(this.spacer_init === false){
            this.spacer_init = true;
            $(this.toolbar).after($('<div id="fixed_toolbar_spacer" />').height(40).hide());
            this.spacer = $('#fixed_toolbar_spacer');
            $('ul', this.toolbar).append('<li class="scroll_top"><a class="item" href="#"></a></li>');
        }
        this.run();
    },
    run: function (){
        handler = function (){
            toolbarScroll.doAutoScroll();
        };
        this.win.off('scroll', handler).on('scroll', handler).trigger('scroll');
    },
    doAutoScroll: function (){
        scroll_top = this.win.scrollTop();
        if (scroll_top > this.offset) {
            if(!$(this.toolbar).hasClass('fixed_toolbar')){
                $(this.toolbar).addClass('fixed_toolbar');
                $(this.spacer).show();
                fit_layout_delta = 30; fitLayout();
            }
        } else {
            $(this.toolbar).removeClass('fixed_toolbar');
            $(this.spacer).hide();
            fit_layout_delta = 0; fitLayout();
        }
    }
};
$(function(){

    if($('#cp_body h1').length > 0){

        hh1 = $('#cp_body h1').offset().top + $('#cp_body h1').height();
        hh2 = $('#cp_footer').offset().top;

        fitLayout();
    }

    $(window).on('resize', function (){
        toolbarScroll.init();
        fitLayout();
    });
    toolbarScroll.init();

    icms.events.on('datagrid_rows_loaded', function (result){
        fitLayout();
        toolbarScroll.init();
    });
    $('.cp_toolbar').on('click', '.scroll_top a', function(event){
        event.preventDefault();
        $('body,html').animate({
            scrollTop: 0 ,
        }, 200);
        return false;
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
        var id = $(this).closest('.field').attr('id').replace(/f_/, '');
        if (typeof(icms.forms.wysiwygs_insert_pool.add[id]) === 'function') {
            icms.forms.wysiwygs_insert_pool.add[id](id, $(this).text()); return false;
        }
        return addTextToPosition($('#'+id), $(this).text(), spacer, spacer_stop);
    });
});