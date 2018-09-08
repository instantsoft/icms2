var fit_layout_delta = 0;
function fitLayout(){
    var h1 = $('#cp_body h1').offset().top + $('#cp_body h1').height();
    var h2 = $('#cp_footer').offset().top;
    $('table.layout').height(h2 - h1 - 2 + fit_layout_delta);
    $('table.layout').width( $('#cp_body').width() + 40 );
}
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
    $(window).on('resize', function (){
        toolbarScroll.init();
        fitLayout();
    });
    toolbarScroll.init();
    fitLayout();
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
});