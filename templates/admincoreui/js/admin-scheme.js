var icms = icms || {};
icms.scheme = (function ($) {

    this.onDocumentReady = function(){

        let cp_widgets_layout = $('#cp-widgets-layout');

        $('#rows_titles_pos input', cp_widgets_layout).on('click', function(e){
            let pos = $(this).val();
            $.cookie('icms[rows_titles_pos]', pos, {expires: 365, path: '/'});
            if(pos === 'left'){
                $('.layout-row-title.layout-row-parent').addClass('col-lg-2');
                $('.layout-row-title').removeClass('d-none').addClass('d-flex');
                $('.layout-row-body.layout-row-parent').addClass('col-lg-10');
            } else if(pos === 'top'){
                $('.layout-row-title.layout-row-parent').removeClass('col-lg-2');
                $('.layout-row-title').removeClass('d-none').addClass('d-flex');
                $('.layout-row-body.layout-row-parent').removeClass('col-lg-10');
            } else if(pos === 'hide'){
                $('.layout-row-title').addClass('d-none').removeClass('col-lg-2 d-flex');
                $('.layout-row-body.layout-row-parent').removeClass('col-lg-10');
            }
        });
        cp_widgets_layout.sortable({
            items: ".widgets-layout-scheme:not(.disable-sortable)",
            opacity: 0.9,
            delay: 150,
            handle: '> .filled > .cell > span',
            connectWith: ".widgets-layout-scheme:not(.disable-sortable)",
            placeholder: 'widgets-layout-scheme-placeholder',
            tolerance:'pointer',
            start: function(event, ui) {
                $(ui.placeholder).height($(ui.item).height());
            },
            update: function(event, ui) {
                let items = new Array();
                $('.widgets-layout-scheme', cp_widgets_layout).each(function(){
                    items.push($(this).data('id'));
                });
                $.post(cp_widgets_layout.data('scheme-row-reorder-url'), {items: items}, function(result){
                    toastr.success(result.success_text);
                }, 'json');
            }
        }).disableSelection();
        $(".widgets-layout-scheme-col-wrap", cp_widgets_layout).sortable({
            items: ".widgets-layout-scheme-col",
            opacity: 0.9,
            delay: 150,
            handle: '.layout-col-title > span',
            connectWith: ".widgets-layout-scheme-col",
            placeholder: 'colplaceholder',
            tolerance:'pointer',
            start: function(event, ui) {
                $(ui.placeholder).addClass($(ui.item).attr('class'));
            },
            update: function(event, ui) {
                let items = new Array();
                $('.widgets-layout-scheme-col-wrap .widgets-layout-scheme-col', cp_widgets_layout).each(function(){
                    items.push($(this).data('id'));
                });
                $.post(cp_widgets_layout.data('scheme-col-reorder-url'), {items: items}, function(result){
                    toastr.success(result.success_text);
                }, 'json');
            }
        }).disableSelection();
    };

    return this;

}).call(icms.scheme || {},jQuery);