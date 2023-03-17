var icms = icms || {};

icms.images = (function ($) {

    this.uploadCallback = null;
    this.removeCallback = null;

    this._onSubmit = function(field_name){
        let widget = $('#widget_image_'+field_name);
        $('.upload', widget).hide();
        $('.loading', widget).show();
    };

    this._showButton = function(field_name){
        let widget = $('#widget_image_'+field_name);
        $('.upload', widget).show();
        $('.loading', widget).hide();
    };

    this._onComplete = function(field_name, result){

        let widget = $('#widget_image_'+field_name);

        if(!result.success) {
            icms.modal.alert(result.error);
            icms.images._showButton(field_name);
            return;
        }

        var preview_img_src = null;
        var image_data = {};

        $('.data', widget).html('');

        var _input_name = $(widget).data('field_name');

        for(var path in result.paths){
            preview_img_src = preview_img_src || result.paths[path].url;
            $('.data', widget).append('<input type="hidden" name="'+_input_name+'['+path+']" value="'+result.paths[path].path+'" />');
            image_data[path] = result.paths[path].path;
        }

        $('.preview img', widget).attr('src', preview_img_src);
        $('.preview', widget).show().data('paths', image_data);
        $('.loading', widget).hide();

        if (typeof(icms.images.uploadCallback) === 'function'){
            icms.images.uploadCallback(field_name, result);
        }
    };

    this._onMultiComplete = function (field_name, result){

        icms.images._showButton(field_name);

        let widget = $('#widget_image_'+field_name);

        if(!result.success) {
            return;
        }

        var idx = $('.data input:last', widget).attr('rel');
        if (typeof(idx) === 'undefined') { idx = 0; } else { idx++; }

        var preview_block = $('.preview_template', widget).clone().removeClass('preview_template').addClass('preview').attr('rel', idx).show();

        var preview_img_src = null;

        var image_data = {};

        var _input_name = $(widget).data('field_name');

        for(var path in result.paths){
            preview_img_src = preview_img_src || result.paths[path].url;
            $('.data', widget).append('<input type="hidden" name="'+_input_name+'['+idx+']['+path+']" value="'+result.paths[path].path+'" rel="'+idx+'" />');
            image_data[path] = result.paths[path].path;
        }

        $(preview_block).data('paths', image_data);
        $('img', preview_block).attr('src', preview_img_src);
        $('a', preview_block).data('id', idx).click(function() { icms.images.removeOne(field_name, this); });

        $('.previews_list', widget).append(preview_block);

    };

    this.uploadByLink = function(field_name, upload_url, link){
        icms.images._onSubmit(field_name);
        var post_params = {}; post_params[field_name] = link;
        $.post(upload_url, post_params, function(result){
            icms.images._onComplete(field_name, result);
        }, 'json');
    };

    this.uploadMultyByLink = function(field_name, upload_url, link, max_images, LANG_UPLOAD_ERR_MAX_IMAGES){
        max_images = +(max_images || 0);
        if(max_images > 0 && icms.images.incrementUploadedCount(field_name) > max_images){
            icms.images.decrementUploadedCount(field_name);
            icms.modal.alert(LANG_UPLOAD_ERR_MAX_IMAGES);
            return false;
        }
        icms.images._onSubmit(field_name);
        var post_params = {}; post_params[field_name] = link;
        $.post(upload_url, post_params, function(result){
            icms.images._onMultiComplete(field_name, result);
        }, 'json');
    };

    this.upload = function(field_name, upload_url){

        return new qq.FileUploader({
            element: document.getElementById('file-uploader-'+field_name),
            action: upload_url,
            multiple: false,
            debug: false,
            showMessage: function(message){
                icms.modal.alert(message);
            },
            onSubmit: function(id, fileName){
                var ftitle = $('#title').val();
                if(ftitle){
                    this.params = {
                        file_name: $('#title').val()+' '+field_name
                    };
                }
                icms.images._onSubmit(field_name);
            },
            onComplete: function(id, file_name, result){
                icms.images._onComplete(field_name, result);
            }
        });
    };

    this.incrementUploadedCount = function (field_name){

        var current_count = +$('#file-uploader-'+field_name).data('uploaded_count');

        current_count += 1;

        $('#file-uploader-'+field_name).data('uploaded_count', current_count);

        return current_count;
    };

    this.decrementUploadedCount = function (field_name){

        var current_count = +$('#file-uploader-'+field_name).data('uploaded_count');

        current_count -= 1;

        $('#file-uploader-'+field_name).data('uploaded_count', current_count);

        return current_count;
    };

    this.createUploader = function(field_name, upload_url, max_images, LANG_UPLOAD_ERR_MAX_IMAGES){

        max_images = +(max_images || 0);

        return new qq.FileUploader({
            element: document.getElementById('file-uploader-'+field_name),
            action: upload_url,
            debug: false,
            showMessage: function(message){
                icms.modal.alert(message);
            },
            onSubmit: function(id, fileName){
                if(max_images > 0 && icms.images.incrementUploadedCount(field_name) > max_images){
                    icms.images.decrementUploadedCount(field_name);
                    icms.modal.alert(LANG_UPLOAD_ERR_MAX_IMAGES);
                    return false;
                }
                var ftitle = $('#title').val();
                if(ftitle){
                    this.params = {
                        file_name: $('#title').val()+' '+field_name
                    };
                }
                icms.images._onSubmit(field_name);
            },
            onComplete: function(id, file_name, result){
                icms.images._onMultiComplete(field_name, result);
            }
        });
    };

    this.initSortable = function (field_name){
        var widget = $('#widget_image_'+field_name);
        var _input_name = $(widget).data('field_name');
        $('.previews_list', widget).sortable({
            items: '.preview',
            cursor: 'move',
            cancel: 'a',
            revert: true,
            opacity: 0.9,
            delay: 150,
            placeholder: 'colplaceholder',
            start: function(event, ui) {
                $(ui.placeholder).addClass($(ui.item).attr('class'));
                $(ui.placeholder).height($(ui.item).height());
                $(ui.placeholder).width($(ui.item).width());
            },
            update: function(event, ui) {
                $('.data', widget).html('');
                $('.previews_list .preview', widget).each(function(index){
                    $(this).attr('rel', index).find('a').data('id', index);
                    var paths = $(this).data('paths');
                    for(var path in paths){
                        $('.data', widget).append('<input type="hidden" name="'+_input_name+'['+index+']['+path+']" value="'+paths[path]+'" rel="'+index+'" />');
                    }

                });
            }
        });
    };

    this.remove = function(field_name){

        var widget = $('#widget_image_'+field_name);

        var delete_url = $(widget).data('delete_url');
        var paths = $('.preview', widget).data('paths');

        $.post(delete_url, {paths: paths, csrf_token: icms.forms.getCsrfToken()}, function(result){

            if (result.error){
                alert(result.message);
                return false;
            }

            $('.preview', widget).hide().data('paths', {});
            $('.preview img', widget).attr('src', '');
            $('.upload', widget).show();
            $('.loading', widget).hide();
            $('.data', widget).html('');

            if (typeof(icms.images.removeCallback) === 'function'){
                icms.images.removeCallback(field_name);
            }
        }, 'json');

        return false;
    };

    this.removeOne = function(field_name, link){

        var idx = $(link).data('id');

        var widget = $('#widget_image_'+field_name);

        var delete_url = $(widget).data('delete_url');
        var paths = $('.preview[rel='+idx+']', widget).data('paths');

        $.post(delete_url, {paths: paths, csrf_token: icms.forms.getCsrfToken()}, function(result){

            if (result.error){
                alert(result.message);
                return false;
            }

            $('.data input[rel='+idx+']', widget).remove();
            $('.preview[rel='+idx+']', widget).remove();

            icms.images.decrementUploadedCount(field_name);

            if (typeof(icms.images.removeCallback) === 'function'){
                icms.images.removeCallback(field_name, idx);
            }
        }, 'json');

        return false;
    };

	return this;

}).call(icms.images || {},jQuery);
