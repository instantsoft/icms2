var RedactorPlugins = RedactorPlugins || {};

RedactorPlugins.smiles = {

    is_loaded: false,
    dropdown: {},

	init: function() {

        var smiles_btn = this.buttonAdd('smiles', this.opts.curLang['smiles'], $.proxy(this.showSmiles, this));

        this.dropdown = $('<div class="redactor_dropdown redactor_dropdown_box_smiles" />');

        smiles_btn.data('dropdown', this.dropdown);

        return;

	},

    load: function () {

        if(this.is_loaded === true){
            return this;
        }

        var _self = this;

        this.showProgressBar();

        $.post(this.opts.smilesUrl, {}, function(result){

            _self.is_loaded = true;

            if(!result.smiles){ return; }

            for(var s in result.smiles){

                var smile_img = $('<img title=":'+s+':" src="'+result.smiles[s]+'" style="display:inline" />');

                _self.dropdown.append(smile_img);

                $(smile_img).on('click', function(e) {

                    e.preventDefault();

                    _self.bufferSet();
                    _self.$editor.focus();

                    _self.insertHtmlAdvanced(this.outerHTML);
                    _self.sync();

                });

            }

            _self.hideProgressBar();

        }, 'json');

        return this;

    },

    showSmiles: function (btnName, $button, btnObject, e){

        this.load();

        this.dropdownShow(e, btnName);

    }

};