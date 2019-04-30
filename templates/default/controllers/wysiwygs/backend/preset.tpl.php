<?php

    $this->addBreadcrumb(LANG_WW_PRESETS, $this->href_to('presets'));

    if ($do=='add'){
        $this->addBreadcrumb(LANG_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($preset['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('presets')
    ));

    $this->renderForm($form, $preset, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
?>

<script>
    $(function(){
        $('#tab-0').remove();
        $('#wysiwyg_name').on('change', function(){

            var wysiwyg_name = $(this).val();

            if(!wysiwyg_name){ return; }

            $.post('<?php echo $this->href_to('wysiwyg_options'); ?>', {
                <?php if ($do=='edit') { ?>
                    preset_id: '<?php echo $preset['id']; ?>',
                <?php } ?>
                wysiwyg_name: wysiwyg_name
            }, function(data){

                $('#tab-basic + .form-tabs').remove();

                if (!data) { return; }

                if(data.error){
                    icms.modal.alert(data.message, 'ui_error'); return;
                }

                $('#tab-basic').after(data.html);

                $('.form-tabs').find('.field.ft_string > input, .field.ft_text > textarea').each(function(indx, element){
                    $(this).trigger('input');
                });

                icms.events.run('loadwwoptions', data);
            }, 'json');
        }).triggerHandler('change');

        $('.form-tabs').on('input', '.field.ft_string > input, .field.ft_text > textarea', function (){
            if($(this).val().length === 0){ return; }
            var btns = $(this).val().split(' ');
            var panel = $(this).closest('.field').find('.pattern_fields');
            $('a', panel).show().css('background-color', '');
            for(var idx in btns){
                var btn = btns[idx].trim();
                if(btn.length < 2){
                    continue;
                }
                $('a:contains("'+btn+'")', panel).filter(function() {
                    var result = $(this).text().trim() === btn;
                    if(!result){
                        var matcher = new RegExp('^'+ btn);
                        if(matcher.test($(this).text())){
                            $(this).css('background-color', '#728994');
                        }
                    }
                    return result;
                }).hide();
            }
        });
    });
</script>