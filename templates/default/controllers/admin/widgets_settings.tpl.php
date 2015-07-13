<div class="modal_form" style="">
<?php

    $this->renderForm($form, $widget, array(
        'action' => $this->href_to('widgets_update'),
        'method' => 'ajax',
        'toolbar' => false
    ), $errors);

?>
</div>
<div class="widget_modal_help_link">
    <a href="<?php echo LANG_HELP_URL_WIDGETS_CFG; ?>" target="_blank"><?php echo LANG_HELP; ?></a>
</div>
<script>

    var w, h;

    setTimeout(function(){

        w = $('.modal_form').width();
        h = 0;

        $('.modal_form #form-tabs .tab').each(function(){
            var th = $(this).height();
            if (th > h){ h = th; }
        });

        h += 120;

        $('.modal_form').css('width', w+'px').css('height', h+'px');

        icms.modal.resize();

    }, 500);

</script>