<h1><?php echo LANG_CONTENT_TYPE; ?>: <span><?php echo $ctype['title']; ?></span></h1>

<?php

    $this->setPageTitle(LANG_CP_CTYPE_FILTERS, $ctype['title']);

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));
    $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
    $this->addBreadcrumb(LANG_CP_CTYPE_FILTERS);

    $this->addMenuItems('ctype', $this->controller->getCtypeMenu('datasets', $ctype['id'])); ?>

<?php
if($table_exists){
    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_FILTER_ADD,
        'href'  => $this->href_to('ctypes', array('filters_add', $ctype['id']))
    ));
    $this->addToolButton(array(
        'class' => 'view_list',
        'title' => LANG_CP_CTYPE_TO_LIST,
        'href'  => $this->href_to('ctypes')
    ));
	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_CTYPES_FILTERS
	));
}
?>

<div class="pills-menu">
    <?php $this->menu('ctype'); ?>
</div>

<?php if(!$table_exists){ ?>
    <p><?php printf(LANG_CP_FILTER_NO_TABLE, $this->href_to('ctypes', array('filters_enable', $ctype['id'])) . '?back=' . href_to_current()); ?></p>
<?php } else { ?>

<?php $this->renderGrid($this->href_to('ctypes', array('filters', $ctype['id'])), $grid); ?>

<?php } ?>