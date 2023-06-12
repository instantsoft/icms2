var icms = icms || {};

icms.modal = (function ($) {

    var self = this;

    var modal_el;

    this.onDocumentReady = function() {
        /** tinymce prevent bootstrap dialog from blocking focusin **/
        $(document).on('focusin', function(e) {
            if ($(e.target).closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root").length) {
                e.stopImmediatePropagation();
            }
        });
        self.render();
        self.bind('a.ajax-modal');
        self.bind('.ajax-modal > a');
        $('.ajax-modal').addClass('ajax-modal-ready');
    };

    this.render = function (){
        $('body').prepend('<div class="modal" id="icms_modal" tabindex="-1" role="dialog" aria-modal="true"><div id="icms-modal-spinner" class="h-100 d-flex"><div class="sk-circle  m-auto"><div class="sk-circle1 sk-child"></div><div class="sk-circle2 sk-child"></div><div class="sk-circle3 sk-child"></div><div class="sk-circle4 sk-child"></div><div class="sk-circle5 sk-child"></div><div class="sk-circle6 sk-child"></div><div class="sk-circle7 sk-child"></div><div class="sk-circle8 sk-child"></div><div class="sk-circle9 sk-child"></div><div class="sk-circle10 sk-child"></div><div class="sk-circle11 sk-child"></div><div class="sk-circle12 sk-child"></div></div></div><div class="modal-dialog modal-lg modal-primary" role="document" style="display: none;"><div class="modal-content"><div class="modal-header"><h4 class="modal-title text-truncate"></h4><button class="btn ml-auto text-white modal-close p-0" type="button" data-dismiss="modal"><svg viewBox="0 0 352 512"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg></button></div><div class="modal-body"></div></div></div></div>');
        modal_el = $('#icms_modal');
        $(modal_el).on('hidden.bs.modal', function (e) {
            self.onHide();
        });
        return this;
    };

    this.onHide = function (){
        $('#icms-modal-spinner', modal_el).addClass('d-flex').removeClass('d-none');
        $(modal_el).find('.modal-dialog').hide().find('.modal-body').html('');
        $(modal_el).find('.modal-title').html('');
        $(modal_el).find('.modal-header').show();
        icms.forms.form_changed = false;
        return this;
    };

    this.showSpinner = function (){
        $(modal_el).modal('show'); return this;
    };

	this.open = function (selector, title, style) {
        $(modal_el).modal('show');
        var parent = $(selector).parent();
        var content = $(selector).detach();
        self.showContent(title, $(content).clone(true).removeClass('d-none').show(), style);
        $(modal_el).on('hidden.bs.modal', function (e) {
            content.appendTo(parent);
        });
    };

    this.close = function(){
        $(modal_el).modal('hide');
    };

    this.showContent = function (title, content, style, style_body){
        style = style || false;
        style_body = style_body || '';
        if(style){
            $(modal_el).find('.modal-dialog').addClass('modal-'+style);
            $(modal_el).on('hidden.bs.modal', function (e) {
                $(modal_el).find('.modal-dialog').removeClass('modal-'+style);
            });
        }
        $('#icms-modal-spinner', modal_el).addClass('d-none').removeClass('d-flex');
        $(modal_el).find('.modal-dialog').show().find('.modal-body').addClass(style_body).html(content);
        $(modal_el).on('hidden.bs.modal', function (e) {
            $(modal_el).find('.modal-body').removeClass(style_body);
        });
        if(title){
            $(modal_el).find('.modal-title').show().html(title);
        } else {
            $(modal_el).find('.modal-title').hide();
        }
    };

	this.bind = function(selector) {

        $(selector).each(function (){

            var url = $(this).attr('href');

            if((new RegExp('[^\.]\.(jpg|jpeg|png|tiff|gif|webp)\\s*$', 'i')).test(url)){
                self.bindGallery(this);
            } else {

                $(this).off('click').on('click', function (){

                    var title = $(this).attr('title');
                    if(!title){
                        title = $(this).data('original-title');
                    }

                    var style = $(this).data('style');
                    var params = $(this).data('params');

                    if(url.charAt(0) === '#'){
                        self.open(url, title, style);
                    } else {
                        self.openAjax(url, params, false, title, style);
                    }

                    return false;
                });

            }

        });

	};

    this.loadPhotoSwipe = function (){
        icms.head.addCss('photoswipe');
        icms.head.addJs('vendors/photoswipe/photoswipe.min', 'photoswipe_ready');
    };

    this.bindGallery = function(selector){
        icms.events.on('photoswipe_ready', function (){
            $(selector).jqPhotoSwipe({
                maxSpreadZoom: 1,
                bgOpacity: 0.85,
                shareEl: false,
                forceSingleGallery: true
            });
        });
        self.loadPhotoSwipe();
    };

	this.openHtml = function(html, title, style, style_body) {
        $(modal_el).modal('show');
		self.showContent(title, html, style, style_body);
	};

    this.openIframe = function(url, title){
        this.openHtml('<div class="embed-responsive"><iframe class="embed-responsive-item" src="'+url+'"></iframe></div>', title, false, 'p-0');
    };

    this.openAjax = function(url, data, open_callback, title, style){

        open_callback = open_callback || false;
        style = style || false;
        title = title || false;
        data = data || {};

        if(data.is_iframe){
            return this.openIframe(url+'?'+$.param(data), title);
        }

        this.showSpinner();

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            beforeSend: function(request) {
                request.setRequestHeader('ICMS-Request-Type', 1);
            },
            success: function(result){
                self.showContent(title, result, style);
                if(open_callback){
                    open_callback();
                }
            },
            error: function(result){
                self.showContent(title, $('#data-wrap', result.responseText).html(), false, 'bg-dark');
            },
            dataType: 'html'
        });

        return false;
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
                    callback();
                });
                break;
        }
    };

    this.resize = function(){
        $(modal_el).modal('handleUpdate');
    };

    this.setHeight = function(height){};

	this.alert = function(text, type) {
        type = type || 'primary';
        type = type.replace('ui_', '');
        type = type.replace('error', 'danger');
		this.openHtml('<div class="alert alert-'+type+' border-0 rounded-0 m-n3">'+text+'</div>', '<b>ⓘ</b>', type);
	};

	return this;

}).call(icms.modal || {},jQuery);
