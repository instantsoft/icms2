var insertSmiles = {
    smiles: [],
    instance: {},
    displayPanel: function (markItUp) {
        this.instance = markItUp;
        this.load();
    },
    load: function () {
        if(this.smiles.length > 0){
            return this.create().display();
        }
        $.post(this.instance.moptions.data.smiles_url, {}, function(result){
            if(!result.smiles){ return; }
            for(var s in result.smiles){
                insertSmiles.smiles.push('<img title=":'+s+':" data-smile=":'+s+':" src="'+result.smiles[s]+'" />');
            }
            insertSmiles.bindCancel().create().display();
        }, 'json');
    },
    create: function (){
        var header = $(this.instance.textarea).closest('div').find('.markItUpHeader');
        if($(header).find('.smilepanel').length == 0){
            $(header).append('<div class="smilepanel">'+insertSmiles.smiles.join('')+'</div>').
                    on('click', '.smilepanel > img', function (){
                        $.markItUp({ replaceWith: ' '+$(this).data('smile')+' ' });
                        insertSmiles.hide();
                    });
        }
        return this;
    },
    hide: function(){
        $(this.instance.textarea).closest('div').find('.smilepanel').hide();
    },
    display: function(){
        $(this.instance.textarea).closest('div').find('.smilepanel').toggle('fast');
    },
    bindCancel: function (){
        $(document).on('click', 'html', function(event) {
            if ($(event.target).closest($('.smilepanel, .btnSmiles, textarea')).length) { return; }
            insertSmiles.hide();
            event.stopPropagation();
        });
        return this;
    }
};