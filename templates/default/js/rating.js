var icms = icms || {};

icms.rating = (function ($) {

    this.options = {}

    this.setOptions = function(options){
        this.options = options;
    }

    //====================================================================//

    this.onDocumentReady = function(){

        $('.rating_widget').each(function(){

            var widget = $(this);

            var controller = widget.data('target-controller');
            var subject = widget.data('target-subject');
            var id = widget.data('target-id');

            $('a.vote-up', widget).click(function(){
                icms.rating.vote('up', controller, subject, id);
            });

            $('a.vote-down', widget).click(function(){
                icms.rating.vote('down', controller, subject, id);
            });

            $('.score span.clickable', widget).on("click", function(){
                var url = widget.data('info-url');
                icms.modal.openAjax(url, {
                   controller: controller,
                   subject: subject,
                   id: id
                });
            });

        });

    }

    //====================================================================//

    this.vote = function(direction, controller, subject, id){

        var widget_id = 'rating-' + subject + '-' + id;
        var widget = $('#'+widget_id);

        $('.arrow', widget).html('<span class="disabled"></span>');
        $('.score', widget).html('<div class="loading-icon"></div>');

        $.post(this.options.url, {

            direction: direction,
            controller: controller,
            subject: subject,
            id: id

        }, function(result){

            if (!result.success){
                $('.score', widget).html('<span class="zero">&mdash;</span>');
                return;
            }

            $('.score', widget).html('<span class="'+result.css_class+'">'+result.rating+'</span>');
            $('.disabled', widget).attr('title', result.message);

        }, "json");

    }

    //====================================================================//

    this.bingInfoPages = function(){

        var widget = $('.rating_info_pagination');

        var controller = widget.data('target-controller');
        var subject = widget.data('target-subject');
        var id = widget.data('target-id');
        var url = widget.data('url');

        $('a', widget).click(function(){

            var link = $(this);
            var page = link.data('page');
            var list = $('#rating_info_window:visible .rating_info_list');

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

    }

    //====================================================================//

    return this;

}).call(icms.rating || {},jQuery);
