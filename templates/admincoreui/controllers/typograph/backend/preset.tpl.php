<?php

    $this->addBreadcrumb(LANG_TYP_PRESETS, $this->href_to(''));

    if ($do=='add'){
        $this->addBreadcrumb(LANG_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($preset['title']);
    }

    $this->addToolButton([
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('')
    ]);

    $this->renderForm($form, $preset, [
        'action' => '',
        'method' => 'post'
    ], $errors);
?>

<script>
    $(function(){

        var dispay_attrs = [];
        var show_attrs = [];

        var options_allowed_tags = $('#options_allowed_tags');
        var form = options_allowed_tags.closest('form');

        $('.form-tabs', form).addClass('without-tabs');

        $(options_allowed_tags).on('change', function(){

            show_attrs = [];
            dispay_attrs = [];

            $('#allowed_tags_attrs .tab-pane', form).each(function(){
                dispay_attrs.push($(this).attr('id'));
            });

            let tags = $(this).val();

            if(!tags){
                tags = [];
            }

            $.post('<?php echo $this->href_to('tags_options'); ?>', {
                <?php if (!empty($preset['id'])) { ?>
                    preset_id: '<?php echo $preset['id']; ?>',
                <?php } ?>
                tags: tags
            }, function(data){

                if (!data) { return; }

                if(data.error){
                    icms.modal.alert(data.message, 'ui_error'); return;
                }

                let fsets = $(data.html);

                if($('#allowed_tags_attrs', form).length === 0){
                    $('#tab-basic').after('<div id="allowed_tags_attrs" />');
                }

                $(fsets).each(function(){
                    if(!$(this).hasClass('tab-pane')){
                        return;
                    }
                    let id = $(this).attr('id');
                    if($('#'+id, form).length === 0){
                        $('#allowed_tags_attrs', form).append(this);
                    }
                    show_attrs.push(id);
                    dispay_attrs.push(id);
                });

                for (let k in dispay_attrs) {
                    if($.inArray(dispay_attrs[k], show_attrs) === -1) {
                        $('#'+dispay_attrs[k]).remove();
                    }
                }

            }, 'json');

        }).triggerHandler('change');

    });
</script>