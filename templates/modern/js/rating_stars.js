var icms = icms || {};

icms.ratingStars = (function ($) {

    var _this = this;

    this.onDocumentReady = function(){

        $('.rating_stars_widget').each(function(){
            _this.bindWidget($(this));
        });

    };

    this.bindWidget = function(widget){

        var options = widget.data();

        $(widget).on('click', '.is_enabled .star.rating', function(){

            var score = $(this).data('rating');
            var stars_wrap = $(this).closest('.icms-stars');

            $(stars_wrap).removeClass('is_enabled');

            $.post(options.url, {
                score:      score,
                controller: options.targetController,
                subject:    options.targetSubject,
                id:         options.targetId
            }, function(result){
                if (!result.success){
                    if (result.message){
                        icms.modal.alert(result.message);
                    }
                    $(stars_wrap).addClass('is_enabled');
                }
                if(result.show_info){
                    $(stars_wrap).addClass('clickable');
                }
                if(result.rating_value){
                    $(stars_wrap).attr('data-stars', result.rating_value);
                }
                if (result.message){
                    $(stars_wrap).attr('title', result.message);
                }
            }, 'json');

            return false;

        });

        $(widget).on('click', '.icms-stars.clickable', function(){
            icms.modal.openAjax(options.infoUrl, {
               controller: options.targetController,
               subject: options.targetSubject,
               id: options.targetId
            }, function(){
                _this.bingInfoPages();
            });
        });

    };

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
            link.addClass('active is-busy');

            $.post(url, {

                controller: controller,
                subject: subject,
                id: id,
                page: page,
                is_list_only: true

            }, function(result){

                list.html(result);
                link.removeClass('is-busy');

            }, "html");

            return false;

        });

    };

    return this;

}).call(icms.ratingStars || {},jQuery);