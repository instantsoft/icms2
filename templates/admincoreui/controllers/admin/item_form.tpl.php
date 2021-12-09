<?php
    $this->addTplJSName('content');

    $this->setPageTitle($page_title);

    $this->addBreadcrumb($page_title);

    if(!empty($show_save_button) || !isset($show_save_button)){
        $this->addToolButton(array(
            'icon'  => 'save',
            'title' => $button_save_text,
            'href'  => "javascript:icms.forms.submit()"
        ));
    }

    if(!$hide_draft_btn){
        $this->addToolButton(array(
            'icon'  => 'bookmark',
            'title' => $button_draft_text,
            'href'  => "javascript:icms.forms.submit('.button.to_draft')"
        ));
    }

    if ($cancel_url){
        $this->addToolButton(array(
            'icon'  => 'window-close',
            'title' => LANG_CANCEL,
            'href'  => $cancel_url
        ));
    }

    $this->renderForm($form, $item, array(
        'action' => '',
        'submit' => array('title' => $button_save_text, 'show' => (isset($show_save_button) ? $show_save_button : true)),
        'cancel' => array('show' => (bool)$cancel_url, 'href' => $cancel_url),
        'buttons' => $hide_draft_btn ? [] : array(
            array(
                'title' => $button_draft_text,
                'name' => 'to_draft',
                'attributes' => array(
                    'type' => 'submit',
                    'class' => 'to_draft btn-warning'
                )
            )
        ),
        'method' => 'post',
        'toolbar' => true,
        'hook' => array(
            'event' => "content_{$ctype['name']}_form_html",
            'param' => array(
                'do' => $do,
                'id' => $do=='edit' ? $item['id'] : null
            )
        ),
    ), $errors);
?>

<?php ob_start(); ?>
<script>
$(function(){
    icms.content.initMultiCats();
    <?php if ($props){ ?>
        icms.content.initProps('<?php echo href_to($ctype['name'], 'props'); ?>'<?php if($do=='edit'){ ?>, <?php echo $item['id']; ?><?php } ?>);
        <?php if ($is_load_props){ ?>
            icms.content.loadProps();
        <?php } ?>
    <?php } ?>
});
</script>
<?php $this->addBottom(ob_get_clean()); ?>