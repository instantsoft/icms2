var icms = icms || {};

icms.photos = (function ($) {

    this.lock = false;
    this.init = false;
    this.mode = '';

    //====================================================================//

    this.onDocumentReady = function(){

        if (this.init == false) { return; }

        if (this.mode == 'album'){

            $('#album-photos-list .delete a').click(function(){
                icms.photos.deletePhoto( $(this).data('id') );
            })

        }

        if (this.mode == 'photo'){

            $('#album-nav').width(slider_w+64);
            $('#photos-slider').width(slider_w);
            $('#photos-slider ul').width(li_w * li_count);

            if (slide_left < min_left) { slide_left = 0; } else
            if (slide_left > max_left) { slide_left = max_left; } else {
                slide_left = slide_left - (li_w * left_li_offset);
            }

            $('#photos-slider ul').css('margin-left', '-'+slide_left+'px');

            icms.photos.toggleArrows();

            $('#album-nav .arr-prev a').click(function(){

                if (icms.photos.lock){ return; }

                if (parseInt($('#photos-slider ul').css('margin-left'), 10) < 0){

                    icms.photos.lock = true;

                    $('#photos-slider ul').animate({marginLeft: '+=' + li_w}, 200, function(){
                        icms.photos.toggleArrows();
                        icms.photos.lock = false;
                    });

                }

            });

            $('#album-nav .arr-next a').click(function(){

                if (icms.photos.lock){ return; }

                if ((-1*max_left) < parseInt($('#photos-slider ul').css('margin-left'), 10)){
                    icms.photos.lock = true;
                    $('#photos-slider ul').animate({marginLeft: '-=' + li_w}, 200, function(){
                        icms.photos.toggleArrows();
                        icms.photos.lock = false;
                    });
                }

            });

        }

    }

    //====================================================================//

    this.toggleArrows = function (){

        if (li_count <= li_in_frame){
            $('#album-nav .arr-prev a').hide();
            $('#album-nav .arr-next a').hide();
            return;
        }

        if (parseInt($('#photos-slider ul').css('margin-left'), 10) == 0){
            $('#album-nav .arr-prev a').hide();
        } else {
            $('#album-nav .arr-prev a').show();
        }

        if ((-1*max_left) == parseInt($('#photos-slider ul').css('margin-left'), 10)){
            $('#album-nav .arr-next a').hide();
        } else {
            $('#album-nav .arr-next a').show();
        }

    }

    //====================================================================//

    this.createUploader = function(upload_url){

        uploader = new qq.FileUploader({
            element: document.getElementById('album-photos-uploader'),
            action: upload_url,
            debug: false,

            onComplete: function(id, file_name, result){

                if(!result.success) { return; }

                var widget = $('#album-photos-widget');
                var preview_block = $('.preview_template', widget).clone().removeClass('preview_template').addClass('preview').attr('rel', result.id).show();

                $('img', preview_block).attr('src', result.url);
                $('.title input', preview_block).attr('name', 'photos['+result.id+']');
                $('a', preview_block).click(function() { icms.photos.remove(result.id); });

                $('.previews_list', widget).append(preview_block);

            }

        });

    }

    //====================================================================//

    this.remove = function(id){

        var widget = $('#album-photos-widget');

        var url = widget.data('delete-url') + '/' + id;

        $.post(url, {}, function(result){

            if (!result.success) { return; }

            $('.preview[rel='+id+']', widget).fadeOut(400, function() { $(this).remove(); });

        }, 'json');

        return false;

    }

    //====================================================================//

    this.deletePhoto = function(id){

        var block = $('#album-photos-list .photo-'+id);

        block.fadeOut(400);

        var url = $('#album-photos-list').data('delete-url') + '/' + id;

        $.post(url, {}, function(result){

        }, 'json');

    }

    this.delete = function(){

        var id = $('#album-photo-item').data('id');
		
        $.post(delete_url, {id: id}, function(result){
			if (result.success){
				window.location.href=result.album_url;
			}
		}, 'json');

    }
	
	this.rename = function(){
		
		var id = $('#album-photo-item').data('id');
		var old_title = $('#album-photo-item').data('title');
		var new_title = prompt(LANG_PHOTOS_RENAME_PHOTO, old_title);
		
		if (!new_title) { return; }
		if (new_title == old_title) { return; }
		
		$.post(rename_url, {title: new_title, id: id}, function(){
			window.location.href='';
		}, 'json');		
		
	}

    //====================================================================//

	return this;

}).call(icms.photos || {},jQuery);
