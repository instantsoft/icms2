<?php

    $this->addTplJSName('content');
    $this->addTplJSName('jquery-chosen');
    $this->addTplCSSName('jquery-chosen');

    $this->setPageTitle($page_title);

    if(!empty($group)){

        $this->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->addBreadcrumb($group['title'], href_to('groups', $group['slug']));
        if ($ctype['options']['list_on']){
            $this->addBreadcrumb((empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile']), href_to('groups', $group['slug'], array('content', $ctype['name'])));
        }

    } else {

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

<?php if ($is_multi_cats) { ?>
	<div class="content_multi_cats_data">
        <?php echo html_select('add_cats', array(), '', array('multiple'=>true)); ?>
	</div>
<?php } ?>

<?php if ($props || $is_multi_cats){ ?>
    <script>
		<?php if ($is_multi_cats){ ?>
			<?php echo $this->getLangJS('LANG_LIST_EMPTY','LANG_SELECT', 'LANG_CONTENT_SELECT_CATEGORIES'); ?>
			var add_cats = []; /** оставлено для совместимости **/
            var add_cats_data = [];
			<?php if (!empty($add_cats)) { ?>
				<?php foreach($add_cats as $cat_id){ ?>
					add_cats_data.push(<?php echo $cat_id; ?>);
				<?php } ?>
			<?php } ?>
            icms.content.initMultiCats(add_cats_data);
		<?php } ?>
		<?php if ($props){ ?>
			<?php echo $this->getLangJS('LANG_LOADING'); ?>
			icms.content.initProps('<?php echo href_to($ctype['name'], 'props'); ?>'<?php if($do=='edit'){ ?>, <?php echo $item['id']; ?><?php } ?>);
			<?php if ($is_load_props){ ?>
				icms.content.loadProps();
			<?php } ?>
		<?php } ?>
    </script>
<?php } ?>