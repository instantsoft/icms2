var icms = icms || {};

icms.images = (function ($) {

    var self = this;

    this.delete_url = '';

    this.uploadCallback = null;
    this.removeCallback = null;
    this.allowed_mime = [];

    this.uploaded_imgs = [];

    this.onDocumentReady = function() {
        $(window).on('beforeunload', function(e) {
            if (!icms.forms.submitted && self.uploaded_imgs.length > 0) {
                for (let key in self.uploaded_imgs) {
                    $.post(self.delete_url, {paths: self.uploaded_imgs[key], csrf_token: icms.forms.getCsrfToken()}, function(result){}, 'json');
                }
            }
        });
    };

    this._onMultiSubmit = function(field_name){
        var widget = $('#widget_image_'+field_name);
        $('.previews_list', widget).append($('.preview_template', widget).clone().removeClass('preview_template').addClass('loading').attr('id', 'img-multi-submit').show());
    };

    this._onSubmit = function(field_name){
        var widget = $('#widget_image_'+field_name);
        $('.upload', widget).hide();
        $('.loading', widget).show();
    };

    this._showButton = function(field_name){
        var widget = $('#widget_image_'+field_name);
        $('.upload', widget).show();
        $('.loading', widget).hide();
    };

    this._onComplete = function(field_name, result){

        var widget = $('#widget_image_'+field_name);

        if(!result.success) {
            icms.modal.alert(result.error);
            self._showButton(field_name);
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

        self.uploaded_imgs.push(image_data);

        if (typeof(self.uploadCallback) === 'function'){
            self.uploadCallback(field_name, result);
        }
    };

    this._onMultiComplete = function (field_name, result){

        $('#img-multi-submit', widget).remove();

        var widget = $('#widget_image_'+field_name);

        if(!result.success) {
            return;
        }

        var idx = $('.data input:last', widget).attr('rel');
        if (typeof(idx) === 'undefined') { idx = 0; } else { idx++; }

        var preview_block = $('.preview_template', widget).clone().removeClass('preview_template').attr('rel', idx).show();

        var preview_img_src = null;

        var image_data = {};

        var _input_name = $(widget).data('field_name');

        for(var path in result.paths){
            preview_img_src = preview_img_src || result.paths[path].url;
            $('.data', widget).append('<input type="hidden" name="'+_input_name+'['+idx+']['+path+']" value="'+result.paths[path].path+'" rel="'+idx+'" />');
            image_data[path] = result.paths[path].path;
        }

        self.uploaded_imgs.push(image_data);

        $(preview_block).data('paths', image_data);
        $('img', preview_block).attr('src', preview_img_src);
        $('a', preview_block).data('id', idx).click(function() { self.removeOne(field_name, this); });

        $('.previews_list', widget).append(preview_block);

    };

    this.uploadByLink = function(field_name, upload_url, link){
        self._onSubmit(field_name);
        var post_params = {}; post_params[field_name] = link;
        $.post(upload_url, post_params, function(result){
            self._onComplete(field_name, result);
        }, 'json');
    };

    this.uploadMultyByLink = function(field_name, upload_url, link, max_images, LANG_UPLOAD_ERR_MAX_IMAGES){
        max_images = +(max_images || 0);
        if(max_images > 0 && self.incrementUploadedCount(field_name) > max_images){
            self.decrementUploadedCount(field_name);
            icms.modal.alert(LANG_UPLOAD_ERR_MAX_IMAGES);
            return false;
        }
        self._onMultiSubmit(field_name);
        var post_params = {}; post_params[field_name] = link;
        $.post(upload_url, post_params, function(result){
            self._onMultiComplete(field_name, result);
        }, 'json');
    };

    this.getRoundedCanvas = function (sourceCanvas) {
        let canvas = document.createElement('canvas');
        let context = canvas.getContext('2d');
        let width = sourceCanvas.width;
        let height = sourceCanvas.height;

        canvas.width = width;
        canvas.height = height;
        context.imageSmoothingEnabled = true;
        context.drawImage(sourceCanvas, 0, 0, width, height);
        context.globalCompositeOperation = 'destination-in';
        context.beginPath();
        context.arc(width / 2, height / 2, Math.min(width, height) / 2, 0, 2 * Math.PI, true);
        context.fill();
        return canvas;
    };

    this.initCropper = function (field_name, upload_url) {

        let wrapper = document.getElementById('file-uploader-' + field_name);
        let input = wrapper.querySelector('.qq-input');
        let image = document.getElementById('cropper-img-' + field_name);
        let modal = $('#modal-crop-' + field_name);
        let image_cropper_rounded = modal.data('image_cropper_rounded');
        let image_cropper_ratio = +modal.data('image_cropper_ratio');
        let cropper;
        let file_name;

        modal.on('shown.bs.modal', function () {
            cropper = new Cropper(image, {
                aspectRatio: image_cropper_ratio,
                viewMode: 2
            });
        }).on('hidden.bs.modal', function () {
            cropper.destroy();
            cropper = null;
        });

        $('#crop-actions-' + field_name+' button').on('click', function () {
            let method = $(this).data('method');
            let option = $(this).data('option');
            cropper[method](option);
            switch (method) {
                case 'scaleX':
                case 'scaleY':
                  $(this).data('option', -option);
                  break;
            }
        });

        $('#crop-' + field_name).on('click', function () {

            let canvas = cropper.getCroppedCanvas();

            if(image_cropper_rounded){
                canvas = self.getRoundedCanvas(canvas);
            }

            canvas.toBlob(function (blob) {

                self._onSubmit(field_name);

                let formData = new FormData();

                formData.append(field_name, blob, file_name);

                let ftitle = $('#title').val();
                if (ftitle) {
                    formData.append('file_name', $('#title').val() + ' ' + field_name);
                }

                $.ajax(upload_url, {
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (result) {
                        self._onComplete(field_name, result);
                    },
                    error: function () {
                        icms.modal.alert('Upload error');
                    }
                });

                modal.modal('hide');
            });

        });

        input.addEventListener('change', function (e) {

            let files = e.target.files;

            let done = function (url) {

                input.value = '';
                image.src = url;

                modal.modal('show');
            };

            if (files && files.length > 0) {

                let file = files[0];

                file_name = file.name;

                let reader = new FileReader();
                reader.onload = function (e) {
                    done(reader.result);
                };
                reader.readAsDataURL(file);
            }
        });
    };

    this.upload = function (field_name, upload_url, allow_image_cropper) {

        if(allow_image_cropper){

            this.initCropper(field_name, upload_url);

            return;
        }

        return new qq.FileUploader({
            element: document.getElementById('file-uploader-' + field_name),
            action: upload_url,
            allowedMime: this.allowed_mime,
            multiple: false,
            debug: false,
            showMessage: function (message) {
                icms.modal.alert(message);
            },
            onSubmit: function (id, fileName) {
                var ftitle = $('#title').val();
                if (ftitle) {
                    this.params = {
                        file_name: $('#title').val() + ' ' + field_name
                    };
                }
                self._onSubmit(field_name);
            },
            onComplete: function (id, file_name, result) {
                self._onComplete(field_name, result);
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

        var uploader = new qq.FileUploader({
            element: document.getElementById('file-uploader-'+field_name),
            action: upload_url,
            allowedMime: this.allowed_mime,
            debug: false,
            showMessage: function(message){
                icms.modal.alert(message);
            },
            onSubmit: function(id, fileName){
                if(max_images > 0 && self.incrementUploadedCount(field_name) > max_images){
                    self.decrementUploadedCount(field_name);
                    icms.modal.alert(LANG_UPLOAD_ERR_MAX_IMAGES);
                    return false;
                }
                var ftitle = $('#title').val();
                if(ftitle){
                    this.params = {
                        file_name: $('#title').val()+' '+field_name
                    };
                }
                self._onMultiSubmit(field_name);
            },
            onComplete: function(id, file_name, result){
                self._onMultiComplete(field_name, result);
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

        $('.preview > .btn', widget).addClass('is-busy');

        var delete_url = $(widget).data('delete_url');
        var paths = $('.preview', widget).data('paths');

        $.post(delete_url, {paths: paths, csrf_token: icms.forms.getCsrfToken()}, function(result){

            $('.preview > .btn', widget).removeClass('is-busy');

            if (result.error){
                alert(result.message);
                return false;
            }

            $('.preview', widget).hide().data('paths', {});
            $('.preview img', widget).attr('src', '');
            $('.upload', widget).show();
            $('.loading', widget).hide();
            $('.data', widget).html('');

            if (typeof(self.removeCallback) === 'function'){
                self.removeCallback(field_name);
            }
        }, 'json');

        return false;
    };

    this.removeOne = function(field_name, link){

        $(link).addClass('is-busy');

        var idx = $(link).data('id');

        var widget = $('#widget_image_'+field_name);

        var delete_url = $(widget).data('delete_url');
        var paths = $('.preview[rel='+idx+']', widget).data('paths');

        $.post(delete_url, {paths: paths, csrf_token: icms.forms.getCsrfToken()}, function(result){

            $(link).removeClass('is-busy');

            if (result.error){
                alert(result.message);
                return false;
            }

            $('.data input[rel='+idx+']', widget).remove();
            $('.preview[rel='+idx+']', widget).remove();

            self.decrementUploadedCount(field_name);

            if (typeof(self.removeCallback) === 'function'){
                self.removeCallback(field_name, idx);
            }
        }, 'json');

        return false;
    };

	return this;

}).call(icms.images || {},jQuery);