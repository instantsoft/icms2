<?php

    $this->addTplJSName('content');

    $this->setPageTitle($page_title);

    if(!$this->isBreadcrumbs()){
        if ($ctype['options']['list_on'] && !$parent){
            $this->addBreadcrumb($ctype['title'], href_to($ctype['name']));
        }
    }

    $this->addBreadcrumb($page_title);

    if(!empty($show_save_button) || !isset($show_save_button)){
        $this->addToolButton(array(
            'class' => 'save',
            'title' => $button_save_text,
            'href'  => "javascript:icms.forms.submit()"
        ));
    }

    if(!$hide_draft_btn){
        $this->addToolButton(array(
            'class' => 'save_draft',
            'title' => $button_draft_text,
            'href'  => "javascript:icms.forms.submit('.button.to_draft')"
        ));
    }

    if ($cancel_url){
        $this->addToolButton(array(
            'class' => 'cancel',
            'title' => LANG_CANCEL,
            'href'  => $cancel_url
        ));
    }

?>

<h1><?php echo html($page_title) ?></h1>

<?php
    $this->renderForm($form, $item, array(
        'action' => '',
        'submit' => array('title' => $button_save_text, 'show' => (isset($show_save_button) ? $show_save_button : true)),
        'cancel' => array('show' => (bool)$cancel_url, 'href' => $cancel_url),
        'buttons' => array(
            array(
                'title' => $button_draft_text,
                'hide' => $hide_draft_btn,
                'name' => 'to_draft',
                'attributes' => array(
                    'type' => 'submit',
                    'class' => 'to_draft'
                )
            )
        ),
        'method' => 'post',
        'toolbar' => false,
        'hook' => array(
            'event' => "content_{$ctype['name']}_form_html",
            'param' => array(
                'do' => $do,
                'id' => $do=='edit' ? $item['id'] : null
            )
        ),
    ), $errors);
?>

<?php if ($is_premoderation && !$is_moderator) { ?>
    <div class="content_moderation_notice icon-info">
        <?php echo LANG_MODERATION_NOTICE; ?>
    </div>
<?php } ?>

<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_LOADING'); ?>
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