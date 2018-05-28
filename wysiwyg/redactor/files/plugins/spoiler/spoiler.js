var RedactorPlugins = RedactorPlugins || {};

RedactorPlugins.spoiler = {

    modal_spoiler: function () {
        return '<section id="redactor-modal-spoiler-insert">'
            + '<form id="redactorInsertSpoilerForm">'
                + '<label>' + this.opts.curLang.spoiler_name + '</label>'
                + '<input type="text" class="redactor_input" id="redactor_spoiler_name" />'
                + '<label>' + this.opts.curLang.spoiler_text + '</label>'
                + '<textarea id="redactor_insert_spoiler_area" style="width: 99%; height: 160px;"></textarea>'
            + '</form>'
        + '</section>'
        + '<footer>'
            + '<button class="redactor_modal_btn redactor_btn_modal_close">' + this.opts.curLang.cancel + '</button>'
            + '<button id="redactor_insert_spoiler_btn" class="redactor_modal_btn redactor_modal_action_btn">' + this.opts.curLang.insert + '</button>'
        + '</footer>'
    },

    init: function () {
        this.buttonAddAfter('link', 'spoiler', this.opts.curLang.spoiler, $.proxy(this.insert, this));
    },

    insert: function () {

        this.selectionSave();

        var callback = $.proxy(function() {

            this.insert_spoiler_node = false;

            var sel = this.getSelectionHtml();
            var spoiler_name = this.opts.curLang.spoiler, text = '';

            var elem = this.getParent();
            var par = $(elem).parent().get(0);
            if (par && par.tagName === 'DIV') {
                elem = par;
            }

            if (elem && $(elem).hasClass('spoiler')) {

                spoiler_name = $(elem).find('label').text();
                text = $(elem).find('.spoiler_body').html();

                text = text.replace(/<br>/gi, '\n');
                text = text.replace(/<span(.*?)id="selection-marker(.*?)<\/span>/gi, '');

                this.insert_spoiler_node = elem;

            } else { text = sel.toString(); }

            $('#redactor_insert_spoiler_area').val(text);

            $('#redactor_spoiler_name').val(spoiler_name);

            $('#redactor_insert_spoiler_btn').on('click', $.proxy(this.spoilerProcess, this));

            setTimeout(function() {
                $('#redactor_spoiler_name').focus();
            }, 200);

        }, this);

        this.modalInit(this.opts.curLang.spoiler, this.modal_spoiler(), 460, callback);

    },

    spoilerProcess: function() {

        var name = $('#redactor_spoiler_name').val();
        var text = $('#redactor_insert_spoiler_area').val();

        this.spoilerInsert(name, text);

    },

    spoilerInsert: function (name, text) {

        this.selectionRestore();

        if (text !== '') {

            text = text.replace(/\n/g, '<br>');

            if (this.insert_spoiler_node) {

                this.bufferSet();

                $(this.insert_spoiler_node).find('label').text(name);
                $(this.insert_spoiler_node).find('.spoiler_body').html(text);

            } else {

                var uniq = Math.random().toString(36).substr(2, 9);

                var spoiler = '<div class="spoiler"><input tabindex="-1" type="checkbox" id="'+uniq+'"><label for="'+uniq+'">'+name+'</label><div class="spoiler_body">'+text+'</div></div>';

                this.insertHtmlAdvanced(spoiler);

            }

            this.sync();

        }

        this.modalClose();

    }

};