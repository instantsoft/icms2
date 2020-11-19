var icms = icms || {};

icms.photos = (function ($) {

    this.page = 1;
    this.initial_page = 1;
    this.has_next = false;
    this.init = false;
    this.is_init_album = {};
    this.mode = '';
    this.big_img = '';
    this.page_img = '';
    this.page_url = '';
    this.row_height = 255;

    this.onDocumentReady = function(){

        if (this.init === false) { return; }

        if (this.mode === 'album'){
            this.initAlbum();
        }

        if (this.mode === 'photo'){
            this.initPhoto();
        }

        if (this.mode === 'edit'){
            icms.modal.bindGallery('.icms-images-preview');
        }

    };

    this.initPhoto = function(){

        $('#bubble .custom-radio').click(function(e) {
            e.stopPropagation();
        });

        $('#bubble input').on('click', function (){
            $('a.process_download').attr('href', $(this).val());
        });
        $('#bubble input:checked').triggerHandler('click');

        this.big_img = $('#photo_container').data('full-size-img');
        this.page_url = $('#photo_container img').data('page-url');

        if (screenfull.isEnabled) {
            $('#fullscreen_photo').removeClass('d-none');
            this.bindFullScreen();
            $(document).on(screenfull.raw.fullscreenchange, function (){
                if(!screenfull.isFullscreen){
                    $('#photo_container > img').attr('src', icms.photos.page_img);
                    $('#fullscreen_photo').removeClass('icms-fullscreen__state_expanded');
                    $('#fullscreen_cont').removeClass('fullscreen_now');
                    $('.photo_navigation').off('click', icms.photos.bindFullScreenNav);
                    var current_page_url = $('#photo_container img').data('page-url');
                    if(current_page_url != icms.photos.page_url){
                        window.location.href = current_page_url;
                    }
                } else {
                    icms.photos.page_img = $('#photo_container img').attr('src');
                    $('#photo_container > img').attr('src', icms.photos.big_img);
                    $('#fullscreen_photo').addClass('icms-fullscreen__state_expanded');
                    $('#fullscreen_cont').addClass('fullscreen_now');
                    $('#photo_container').on('click', '.photo_navigation', icms.photos.bindFullScreenNav);
                }
            });
        } else {
            if(icms.photos.big_img){
                var click_link = $('#photo_container').addClass('full_in_modal').find('.fullscreen_click').show().attr('href', icms.photos.big_img);
                icms.modal.bindGallery(click_link);
            }
        }

    };

    this.bindFullScreen = function (){
        $(document).on('click', '#fullscreen_photo', function (){
            screenfull.toggle($('#fullscreen_cont')[0]);
            return false;
        });
        $(document).keyup(function(event){
            if(event.keyCode === 39){
                try{ $('#photo_container .photo_navigation.next_item').trigger('click');}catch(e){}
            }
            if(event.keyCode === 37){
                try{ $('#photo_container .photo_navigation.prev_item').trigger('click'); }catch(e){}
            }
        });
    };

    this.bindFullScreenNav = function () {

        $(this).addClass('is-busy');

        $.get($(this).attr('href'), function(data){
           $('#photo_container').html(data);
        }, 'html');

        return false;
    };

    this.initAlbum = function(selector){

        selector = selector || '#album-photos-list';

        if (this.is_init_album[selector] === true) { return; }

        $(selector).on('click', 'a.icms-photo-album__photo_delete', function(){
            return icms.photos.deletePhoto($(this).data('id'), this);
        });

        this.is_init_album[selector] = true;
    };

    this.initCarousel = function (selector, init_callback){
        init_callback = init_callback || function(){};
        $(selector).slick({
            infinite: false,
            arrows: false,
            dots: true,
            variableWidth: true,
            slidesToShow: 3,
            slidesToScroll: 2,
            responsive: [
                {breakpoint: 650, settings: {slidesToShow: 3, slidesToScroll: 1, dots: false}},
                {breakpoint: 320, settings: {slidesToShow: 2, slidesToScroll: 1, dots: false}}
            ]
        });
    };

    this.createUploader = function(upload_url, onSubmit){

        onSubmit = onSubmit || function(){ return true; };

        var uploader = new qq.FileUploader({
            element: document.getElementById('album-photos-uploader'),
            action: upload_url,
            debug: false,
            onSubmit: onSubmit,
            onComplete: function(id, file_name, result){

                if(!result.success) { return; }

                var widget = $('#album-photos-widget');
                var preview_block = $('.preview_template', widget).clone().
                        removeClass('preview_template').addClass('preview').attr('rel', result.id).show();

                $('img', preview_block).attr('src', result.url);
                icms.modal.bind($('.hover_image', preview_block).attr('href', result.big_url));

                $('.photo_privacy select', preview_block).attr('name', 'is_private['+result.id+']');
                $('.photo_type select', preview_block).attr('name', 'type['+result.id+']');

                $('.title input', preview_block).attr('name', 'photos['+result.id+']');
                if(result.name){
                    var pos, p = result.name.indexOf('.');
                    while (pos != -1) { pos = result.name.indexOf('.',pos+1); if(pos != -1){ p = pos; } }
                    $('.title input', preview_block).val(result.name.substring(0, p));
                }

                $('.photo_content textarea', preview_block).
                        attr('name', 'content['+result.id+']').
                        attr('id', 'mcontent_'+result.id);

                $('.actions a.delete', preview_block).click(function() { return icms.photos.remove(result.id); });

                $('.previews_list', widget).append(preview_block);

                var wysiwyg_name = $(widget).data('wysiwyg_name');

                if(wysiwyg_name){
                    window['init_'+wysiwyg_name]('mcontent_'+result.id);
                }

            }

        });

    };

    this.remove = function(id){

        var widget = $('#album-photos-widget');

        var url = widget.data('delete-url') + '/' + id;

        $.post(url, {}, function(result){

            if (!result.success) { return; }

            $('.preview[rel='+id+']', widget).fadeOut('fast', function() { $(this).remove(); });

        }, 'json');

        return false;

    };

    this.deletePhoto = function(id, link){

        if(!confirm(LANG_PHOTOS_DELETE_PHOTO_CONFIRM)){
            return false;
        }

        var block = $(link).closest('.icms-photo-album__photo');
        var photo_wrap = $(link).closest('.album-photos-wrap');

        $.post($(photo_wrap).data('delete-url') + '/' + id, {}, function(result){
            block.fadeOut('fast', function (){
                $(this).remove();
                icms.photos.flexImagesInit('#'+$(photo_wrap).attr('id'));
            });
        }, 'json');

        return false;

    };

    this.delete = function(){

        $.post($('#album-photo-item').data('item-delete-url'), {id: $('#album-photo-item').data('id')}, function(result){
			if (result.success){
				window.location.href=result.album_url;
			}
		}, 'json');

    };

	this.flexImagesInit = function(selector){
        selector = selector || '#album-photos-list';
        $(selector).flexImages({rowHeight: this.row_height, container: '.icms-photo-album__photo', truncate: this.has_next});
	};

    this.showMore = function(link){

        var photo_wrap = $(link).prev('.album-photos-wrap');

        if(!icms.photos.has_next){
            if(icms.photos.initial_page > 1){
                return true;
            }
            $('body,html').animate({
                scrollTop: $(photo_wrap).offset().top
                }, 500
            );
            return false;
        }

        $(link).addClass('is-busy');

        icms.photos.page += 1;

        var post_params = $(link).data('url-params');
        post_params.photo_page = icms.photos.page;

        $.post($(link).data('url'), post_params, function(html){

            var first_page_url = $(link).data('first-page-url');

            $(link).removeClass('is-busy');

            if (!html) { return; }

            $(photo_wrap).append(html);

            if(!icms.photos.has_next){
                $('span', link).html($('span', link).data('to-first'));
                $(link).attr('href', first_page_url);
            }

            var _sep = first_page_url.indexOf('?') !== -1 ? '&' : '?';

            window.history.pushState({link: first_page_url+_sep+'photo_page='+icms.photos.page}, '', first_page_url+_sep+'photo_page='+icms.photos.page);

        }, 'html');

        return false;

    };

	return this;

}).call(icms.photos || {},jQuery);