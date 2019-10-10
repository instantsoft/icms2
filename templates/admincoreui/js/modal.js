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

    this.showModalContent = function (title, content, style){
        style = style || false;
        $('#icms_modal').find('.modal-dialog').removeClass('modal-danger modal-warning');
        if(style){
            $('#icms_modal').find('.modal-dialog').addClass('modal-'+style);
        }
        $('#icms_modal #icms-modal-spinner').addClass('d-none').removeClass('d-flex');
        $('#icms_modal').find('.modal-dialog').fadeIn('fast').find('.modal-body').html(content);
        if(title){
            $('#icms_modal').find('.modal-header').show().find('.modal-title').html(title);
        } else {
            $('#icms_modal').find('.modal-header').hide();
        }
    };

	this.bind = function(selector) {
        $(selector).on('click', function (){

            var _this = this;

            icms.modal.showModalSpinner();

            $.get($(_this).attr('href'), function(result){

                var title = $(_this).attr('title');
                if(!title){
                    title = $(_this).data('original-title');
                }

                icms.modal.showModalContent(title, result);
            }, 'html');

            return false;
        });
	};

	this.open = function(selector) {
        $(selector).modal('show');
	};

	this.openHtml = function(html, title, style) {
		icms.modal.showModalContent(title, html, style);
	};

    this.openAjax = function(url, data, open_callback, title){

        open_callback = open_callback || function(){};
        title = title || '';

        if (typeof(data)=='undefined'){
            $.nmManual(url, {autoSizable: true, anim: {def: 'show'}, callbacks: {afterShowCont: open_callback, initFilters : function (nm) {
                if(title){ nm.opener.attr('title', title); nm.filters.push('title'); }
            }}});
            return false;
        }

        $.nmManual(url+(data.is_iframe ? '?'+$.param(data) : ''), {autoSizable: true, anim: {def: 'show'}, callbacks: {afterShowCont: open_callback, initFilters : function (nm) {
                if(title){ nm.opener.attr('title', title); nm.filters.push('title'); }
                if(data.is_iframe){ nm.filters.push('link'); nm.filters.push('iframe'); }
        }}, ajax:{data: data, type: "POST"}});
        return false;

    };

    this.bindGallery = function(selector){
        $(selector).attr('rel', 'gal');
        $(selector).nyroModal({anim: {def: 'show'}});
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