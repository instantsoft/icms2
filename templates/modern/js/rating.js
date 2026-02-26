var icms = icms || {};

icms.rating = (function ($) {

    let self = this;

    this.options = {};

    this.setOptions = function(options){
        this.options = options;
    };

    this.onDocumentReady = function(){
        $('.rating_widget').each(function(){
            self.bindWidget($(this));
        });
    };

    this.bindWidget = function(widget){

        let controller = widget.data('target-controller');
        let subject = widget.data('target-subject');
        let id = widget.data('target-id');

        $('.arrow-btn', widget).off('click');

        $('a.vote-up', widget).on('click', function(){
            return self.vote('up', controller, subject, id);
        });

        $('a.vote-down', widget).on('click', function(){
            return self.vote('down', controller, subject, id);
        });

        $('.vote-clear', widget).on('click', function(){
            return self.vote('clear', controller, subject, id);
        });

        $('.score span.clickable', widget).off('click').on('click', function(){
            let url = widget.data('info-url');
            icms.modal.openAjax(url, {
               controller: controller,
               subject: subject,
               id: id
            }, function(){
                self.bingInfoPages();
            });
        });
    };

    this.vote = function(direction, controller, subject, id){

        let widget_id = 'rating-' + subject + '-' + id;
        let widget = $('#'+widget_id);

        $('.arrow-btn', widget).addClass('btn-click__busy');

        $.post(this.options.url, {

            direction: direction,
            controller: controller,
            subject: subject,
            id: id

        }, function(result){

            $('.arrow-btn', widget).removeClass('btn-click__busy');

            if (!result.success){
                if (result.message){
                    icms.modal.alert(result.message);
                    $('.disabled', widget).attr('title', result.message);
                }
                if (result.rating){
                    $('.score', widget).html('<span class="'+result.css_class+'">'+result.rating+'</span>');
                }
                return;
            }

            $('.score', widget).html('<span class="'+result.css_class+'">'+result.rating+'</span>');
            $('.arrow-btn', widget).toggleClass('d-none');
            $('.disabled', widget).attr('title', result.message);
            $('.disabled', widget).removeClass('text-success text-danger text-secondary vote-clear clickable');

            if (direction === 'up') {
                $('.disabled-up', widget).addClass('text-success');
                $('.disabled-down', widget).addClass('text-secondary');
            }
            if (direction === 'down') {
                $('.disabled-up', widget).addClass('text-secondary');
                $('.disabled-down', widget).addClass('text-danger');
            }
            if (result.is_allow_change){
                $('.disabled-'+direction, widget).addClass('vote-clear clickable');
            }

            self.bindWidget(widget);

        }, 'json');

        return false;
    };

    this.bingInfoPages = function(){

        let widget = $('.rating_info_pagination');

        let controller = widget.data('target-controller');
        let subject = widget.data('target-subject');
        let id = widget.data('target-id');
        let url = widget.data('url');

        $('a', widget).click(function(){

            let link = $(this);
            let page = link.data('page');
            let list = $('#rating_info_window:visible .rating_info_list');

            $('a', widget).removeClass('active');
            link.addClass('active');

            list.addClass('loading-panel');

            $.post(url, {

                controller: controller,
                subject: subject,
                id: id,
                page: page,
                is_list_only: true

            }, function(result){

                list.html(result).removeClass('loading-panel');

            }, "html");

            return false;
        });
    };

    return this;

}).call(icms.rating || {},jQuery);