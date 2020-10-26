<?php
    $this->addMenuItems('group_tabs', $this->controller->getGroupEditMenu($group));
?>

<?php $this->menu('group_tabs', true, 'icms-groups__tabs nav nav-tabs my-3'); ?>
<?php
$this->addTplJSNameFromContext('vendors/slick/slick.min');
$this->addTplCSSNameFromContext('slick');
ob_start();
?>
<script>
    icms.menu.initSwipe('.icms-groups__tabs', {variableWidth: true, initialSlide: $('.icms-groups__tabs > li.is-active').index()});
</script>
<?php $this->addBottom(ob_get_clean()); ?>