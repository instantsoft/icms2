<?php
    $this->addMenuItems('group_tabs', $this->controller->getGroupEditMenu($group));
?>
<div class="mobile-menu-wrapper mobile-menu-wrapper__tab my-3">
    <?php $this->menu('group_tabs', true, 'icms-groups__tabs nav nav-tabs'); ?>
</div>