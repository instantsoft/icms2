var icms = icms || {};

icms.slider = (function ($) {

    this.stopped = []

    this.onDocumentReady = function() {

        $('.widget_content_slider').each(function(){

            var slider = $(this);
            var id = slider.data('id');
            var delay = Number(slider.data('delay')) * 1000;

            var currentSlide = 0;
            var totalSlides = $('.item', slider).length;

            $('.item', slider).click(function(){
                icms.slider.showSlide(slider, this, false);
                return false;
            })

            setTimeout('icms.slider.nextSlide('+id+', '+currentSlide+', '+totalSlides+', '+delay+')', delay);

        });

    }

    //=====================================================================//

    this.nextSlide = function (id, currentSlide, totalSlides, delay){

        if (this.stopped.indexOf(id) > -1) { return false; }

        currentSlide += 1;

        if (currentSlide == totalSlides) { currentSlide = 0; }

        var slider = $('#content-slider-'+id);

        var item = $('.item', slider).eq(currentSlide);

        this.showSlide(slider, item, true);

        setTimeout('icms.slider.nextSlide('+id+', '+currentSlide+', '+totalSlides+', '+delay+')', delay);

    }

    //=====================================================================//

    this.showSlide = function(slider, item, is_auto){

        item = $(item);

        $('.item', slider).removeClass('active');
        item.addClass('active');

        var url = $('.data .url', item).html();
        var teaser = $('.data .teaser', item).html();
        var title = $('.title', item).html();

        $('.slide img:visible', slider).fadeOut(500);
        $('.slide-'+item.data('id'), slider).fadeIn(500);

        $('.slide a', slider).attr('href', url);
        $('.slide .heading h2', slider).html(title);
        $('.slide .heading .teaser', slider).html(teaser);

        if (!is_auto) { this.stopped.push(slider.data('id')); }

    }

    //=====================================================================//

	return this;

}).call(icms.slider || {},jQuery);
