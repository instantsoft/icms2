var icms = icms || {};

icms.modal = (function ($) {

    this.onDocumentReady = function() {
        icms.modal.bind('a.ajax-modal');
        icms.modal.bind('.ajax-modal a');
    };

    //====================================================================//

	this.bind = function(selector) {
        $(selector).nyroModal({anim: {def: 'show'}});
	};

    //====================================================================//

	this.open = function(selector) {
		$.nmManual(selector, {autoSizable: true, anim: {def: 'show'}});
	};

    //====================================================================//

	this.openHtml = function(html) {
		$.nmData(html, {autoSizable: true, anim: {def: 'show'}});
	};

    //====================================================================//

    this.openAjax = function(url, data, open_callback){

        open_callback = open_callback || function(){};

        if (typeof(data)=='undefined'){
            $.nmManual(url, {autoSizable: true, anim: {def: 'show'}, callbacks: {afterShowCont: open_callback}});
            return false;
        }

        $.nmManual(url, {autoSizable: true, anim: {def: 'show'}, callbacks: {afterShowCont: open_callback}, ajax:{data: data, type: "POST"}});
        return false;

    };

    //====================================================================//

    this.bindGallery = function(selector){
        $(selector).attr('rel', 'gal');
        $(selector).nyroModal({anim: {def: 'show'}});
    };

    //====================================================================//

    this.close = function(){
        $.nmTop().close();
    };

    //====================================================================//

    this.setCallback = function(event, callback){
        switch(event){
            case 'open':
                $.nmTop().callbacks.afterShowCont = callback; break;
            case 'close':
                $.nmTop().callbacks.beforeClose = callback; break;
        }
    };

    //====================================================================//

    this.resize = function(){
        $.nmTop().resize(true);
    };

    this.setHeight = function(height){
        $('.nyroModalCont').css('height', height+'px');
    };

	this.alert = function(text, type) {
        type = type || '';
		this.openHtml('<div id="alert_wrap"><div class="ui_message '+type+'">'+text+'</div></div>');
	};

	return this;

}).call(icms.modal || {},jQuery);