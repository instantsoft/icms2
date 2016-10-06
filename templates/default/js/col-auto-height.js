colAutoHeight = {
    wrap: {},
    col: '> *',
    wrap_width: 0,
    row_width: 0,
    highest_box: 0,
    cols: $([]),
    init: function(wrap_selector, col_selector){
        this.col = col_selector || this.col;
        this.wrap = $(wrap_selector);
        $(window).on('resize', function(event) {
            colAutoHeight.calc();
        });
        return this.calc();
    },
    clear: function(){
        this.row_width = 0;
        this.highest_box = 0;
        this.cols = $([]);
        return this;
    },
    setHeight: function (){
        if(this.cols.length){
            this.cols.height(this.highest_box);
        } else {
            $(this.col, this.wrap).last().height('auto');
        }
        return this;
    },
    calc: function (){
        this.wrap_width = this.wrap.width();
        $(this.col, this.wrap).each(function (){
            if($(this).height() > colAutoHeight.highest_box) {
                colAutoHeight.highest_box = $(this).height();
            }
            if((colAutoHeight.row_width + $(this).width() + 30) > colAutoHeight.wrap_width){
                colAutoHeight.setHeight().clear();
            } else {
                colAutoHeight.row_width += $(this).width();
                colAutoHeight.cols = colAutoHeight.cols.add(this);
            }
        });
        return this.setHeight();
    }
};