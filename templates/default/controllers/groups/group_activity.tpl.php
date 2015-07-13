<?php

    $this->setPageTitle(LANG_GROUPS_PROFILE_ACTIVITY, $group['title']);

    $this->addBreadcrumb(LANG_GROUPS, href_to('groups'));
    $this->addBreadcrumb($group['title'], $this->href_to($group['id']));
    $this->addBreadcrumb(LANG_GROUPS_PROFILE_ACTIVITY);

?>

<div id="group_profile_header">
    <?php $this->renderChild('group_header', array('group'=>$group)); ?>
</div>

<?php echo $html; ?>
