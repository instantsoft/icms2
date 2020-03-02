var icms = icms || {};

icms.modal = (function ($) {

    this.onDocumentReady = function() {
        icms.modal.bind('a.ajax-modal');
        icms.modal.bind('.ajax-modal > a');
        $(document).on('hidden.bs.modal', '#icms_modal', function (e) {
            $('#icms_modal #icms-modal-spinner').addClass('d-flex').removeClass('d-none');
            $('#icms_modal').find('.modal-dialog').hide().find('.modal-body').html('');
            $('#icms_modal').find('.modal-header').show();
        });
        icms.modal.renderModal();
    };

    this.renderModal = function (){
        $('body').append('<div class="modal" id="icms_modal" tabindex="-1" role="dialog" aria-modal="true"><div id="icms-modal-spinner" class="h-100 d-flex"><div class="sk-circle  m-auto"><div class="sk-circle1 sk-child"></div><div class="sk-circle2 sk-child"></div><div class="sk-circle3 sk-child"></div><div class="sk-circle4 sk-child"></div><div class="sk-circle5 sk-child"></div><div class="sk-circle6 sk-child"></div><div class="sk-circle7 sk-child"></div><div class="sk-circle8 sk-child"></div><div class="sk-circle9 sk-child"></div><div class="sk-circle10 sk-child"></div><div class="sk-circle11 sk-child"></div><div class="sk-circle12 sk-child"></div></div></div><div class="modal-dialog modal-lg modal-primary" role="document" style="display: none;"><div class="modal-content"><div class="modal-header"><h4 class="modal-title"></h4><button class="close" type="button" data-dismiss="modal"><span aria-hidden="true">×</span></button></div><div class="modal-body"></div></div></div></div>');
        return this;
    };

    this.showModalSpinner = function (){
        $('#icms_modal').modal('show'); return this;
    };

    this.showModalContent = function (title, content, style, style_body){
        style = style || false;
        style_body = style_body || '';
        $('#icms_modal').find('.modal-dialog').removeClass('modal-danger modal-warning');
        if(style){
            $('#icms_modal').find('.modal-dialog').addClass('modal-'+style);
        }
        $('#icms_modal #icms-modal-spinner').addClass('d-none').removeClass('d-flex');
        $('#icms_modal').find('.modal-dialog').fadeIn('fast').find('.modal-body').addClass(style_body).html(content);
        if(title){
            $('#icms_modal').find('.modal-header').show().find('.modal-title').html(title);
        } else {
            $('#icms_modal').find('.modal-header').hide();
        }
    };

	this.bind = function(selector) {

        $(selector).each(function (){

            var url = $(this).attr('href');

            if((new RegExp('[^\.]\.(jpg|jpeg|png|tiff|gif|bmp)\\s*$', 'i')).test(url)){
                $(this).jqPhotoSwipe({
                    galleryOpen: function (gallery) {
                        gallery.toggleDesktopZoom();
                    },
                    maxSpreadZoom: 1,
                    bgOpacity: 0.85,
                    shareEl: false,
                    forceSingleGallery: true
                });
            } else {

                $(this).off('click').on('click', function (){

                    var title = $(this).attr('title');
                    if(!title){
                        title = $(this).data('original-title');
                    }

                    if(url.charAt(0) === '#'){
                        $('#icms_modal').modal('show');
                        icms.modal.showModalContent(title, $(url).html());
                    } else {
                        icms.modal.openAjax(url, {}, false, title);
                    }

                    return false;
                });

            }

        });

	};

	this.open = function(selector) {
        $(selector).modal('show');
	};

	this.openHtml = function(html, title, style, style_body) {
        $('#icms_modal').modal('show');
		icms.modal.showModalContent(title, html, style, style_body);
	};

    this.openIframe = function(url, title){

    };

    this.openAjax = function(url, data, open_callback, title){

        open_callback = open_callback || false;
        title = title || false;
        data = data || {};

        if(data.is_iframe){
            return this.openIframe(url+'?'+$.param(data), title);
        }

        this.showModalSpinner();

        $.ajax({
            url: url,
            data: data,
            success: function(result){
                icms.modal.showModalContent(title, result);
                if(open_callback){
                    open_callback();
                }
            },
            error: function(result){
                icms.modal.showModalContent(title, $('#data-wrap', result.responseText).html());
            },
            dataType: 'html'
        });

        return false;

    };

    this.bindGallery = function(selector){
        $(selector).jqPhotoSwipe({
            forceSingleGallery: true
        });
    };

    this.close = function(){
        $('#icms_modal').modal('hide');
    };

    this.setCallback = function(event, callback){
        switch(event){
            case 'open':
                $(document).on('shown.bs.modal', '#icms_modal', function (e) {
                    callback();
                });
                break;
            case 'close':
                $(document).on('hidden.bs.modal', '#icms_modal', function (e) {
                    icms.forms.form_changed = false;
                    callback();
                });
                break;
        }
    };

    this.resize = function(){
        $('#icms_modal').modal('handleUpdate');
    };

    this.setHeight = function(height){};

	this.alert = function(text, type) {
        type = type || '';
        type = type.replace('ui_', '');
        type = type.replace('error', 'danger');
		this.openHtml(text, '<i class="fa fa-warning fa-lg"></i>', type);
	};

	return this;

}).call(icms.modal || {},jQuery);