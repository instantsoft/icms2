var InlineUpload = {
    dialog: null,
    block: '',
    offset: {},
    options: {
        container_class: 'markItUpInlineUpload',
        form_id: 'inline_upload_form',
        action: '',
        inputs: {
            file: {
                id: 'inline_upload_file',
                name: 'inline_upload_file'
            }
        },
        submit: {
            id: 'inline_upload_submit',
            value: ''
        },
        close: 'inline_upload_close',
        iframe: 'inline_upload_iframe'
    },
    display: function(hash){

        if ($('.markItUpInlineUpload').length) { this.cleanUp(); return false; }

        var self = this;

        this.offset = $(hash.textarea).prev('.markItUpHeader').offset();

        this.options.action = hash.moptions.data.upload_url;
        this.options.submit.value = hash.moptions.data.upload_title;

        this.dialog = $([
            '<div class="',
            this.options.container_class,
            '"><div><form id="',
            this.options.form_id,
            '" action="',
            this.options.action,
            '" target="',
            this.options.iframe,
            '" method="post" enctype="multipart/form-data">',
            '<input name="',
            this.options.inputs.file.name,
            '" id="',
            this.options.inputs.file.id,
            '" type="file" /><input id="',
            this.options.submit.id,
            '" type="button" value="',
            this.options.submit.value,
            '" /></form><div id="',
            this.options.close,
            '"></div><iframe id="',
            this.options.iframe,
            '" name="',
            this.options.iframe,
            '" src="about:blank"></iframe></div></div>',
            ].join(''))
        .appendTo(document.body)
        .hide()
        .css('top', this.offset.top)
        .css('left', this.offset.left);

        $('#'+this.options.submit.id).click(function(){
            $('#'+self.options.form_id).submit().fadeTo('fast', 0.2);
        });

        $('#'+this.options.close).click(function() { self.cleanUp() });

        $('#'+this.options.iframe).bind('load', function(){

            var json = $(this).contents().find('pre').text();

            if (!json){ json = $(this).contents().text(); }
            if (!json){ return false; }

            var response = JSON.parse(json);

            if (response.success) {

                this.block = [ '<img src="', response.image.url, '" />' ];
                self.cleanUp();
                $.markItUp({
                    replaceWith: this.block.join('')
                } );

            } else {
                alert(response.error);
                self.cleanUp();
            }
        });

        this.dialog.fadeIn('fast');
    },
    cleanUp: function(){
        $('.markItUpInlineUpload').fadeOut('fast', function(){ $('.markItUpInlineUpload').remove(); });
    }
};