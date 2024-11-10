<?php
    $this->addTplJSName('content');

    $this->setPageTitle($page_title);

    $this->addBreadcrumb($page_title);

    if(!empty($show_save_button) || !isset($show_save_button)){
        $this->addToolButton([
            'class' => 'save process-save',
            'title' => LANG_SAVE,
            'href'  => '#',
            'icon'  => 'save'
        ]);
    }

    if(!$hide_draft_btn){
        $this->addToolButton([
            'class' => 'process-save',
            'icon'  => 'bookmark',
            'data'  => ['submit_class' => '.button.to_draft'],
            'title' => $button_draft_text,
            'href'  => '#'
        ]);
    }

    if ($cancel_url){
        $this->addToolButton([
            'class' => 'cancel',
            'title' => LANG_CANCEL,
            'href'  => $cancel_url,
            'icon'  => 'undo'
        ]);
    }

    $this->renderForm($form, $item, [
        'action' => '',
        'submit' => ['title' => $button_save_text, 'show' => ($show_save_button ?? true)],
        'cancel' => ['show' => (bool)$cancel_url, 'href' => $cancel_url],
        'buttons' => $hide_draft_btn ? [] : [
            [
                'title' => $button_draft_text,
                'name' => 'to_draft',
                'attributes' => [
                    'type' => 'submit',
                    'class' => 'to_draft btn-warning'
                ]
            ]
        ],
        'method' => 'post',
        'toolbar' => true,
        'hook' => [
            'event' => "content_{$ctype['name']}_form_html",
            'param' => [
                'do' => $do,
                'id' => $do=='edit' ? $item['id'] : null
            ]
        ]
    ], $errors);
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