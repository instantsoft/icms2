var icms = icms || {};

icms.modal = (function ($) {

    this.onDocumentReady = function() {
        icms.modal.bind('a.ajax-modal');
        icms.modal.bind('.ajax-modal a');
    }

    //====================================================================//

	this.bind = function(selector) {
        $(selector).nyroModal();
	}

    //====================================================================//

	this.open = function(selector) {
		$.nmManual(selector);
	}

    //====================================================================//

	this.openHtml = function(html) {
		$.nmData(html);
	};

    //====================================================================//

    this.openAjax = function(url, data){

        if (typeof(data)=='undefined'){
            $.nmManual(url, {autoSizable: true});
            return false;
        }

        $.nmManual(url, {autoSizable: true, ajax:{data: data, type: "POST"}});
        return false;

    }

    //====================================================================//

    this.bindGallery = function(selector){
        $(selector).attr('rel', 'gal');
        $(selector).nyroModal();
    }

    //====================================================================//

    this.close = function(){
        $.nmTop().close();
    }

    //====================================================================//

    this.setCallback = function(event, callback){
        switch(event){
            case 'open':
                $.nmTop().callbacks.afterShowCont = callback; break;
            case 'close':
                $.nmTop().callbacks.beforeClose = callback; break;
        }
    }

    //====================================================================//

    this.resize = function(){
        $.nmTop().resize(true);
    }

    this.setHeight = function(height){
        $('.nyroModalCont').css('height', height+'px');
    }

	return this;

}).call(icms.modal || {},jQuery);
