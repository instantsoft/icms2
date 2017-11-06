var insertSmiles = {
    smiles: [],
    smiles_url: '',
    instance: {},
    displayPanel: function (markItUp) {
        this.instance = markItUp;
        this.smiles_url = $(this.instance.textarea).data('smiles-url');
        this.load();
    },
    load: function () {
        if(this.smiles.length > 0){
            return this.display();
        }
        $.post(this.smiles_url, {}, function(result){
            if(!result.smiles){ return; }
            for(var s in result.smiles){
                insertSmiles.smiles.push('<img title=":'+s+':" data-smile=":'+s+':" src="'+result.smiles[s]+'" />');
            }
            insertSmiles.bindCancel().create().display();
        }, 'json');
    },
    create: function (){
        $(this.instance.textarea).parent().find('.markItUpHeader').
                append('<div class="smilepanel">'+insertSmiles.smiles.join('')+'</div>').
                on('click', '.smilepanel > img', function (){
                    $.markItUp({ replaceWith: ' '+$(this).data('smile')+' ' });
                    insertSmiles.hide();
                });
        return this;
    },
    hide: function(){
        $(this.instance.textarea).parent().find('.smilepanel').hide();
    },
    display: function(){
        $(this.instance.textarea).parent().find('.smilepanel').toggle('fast');
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