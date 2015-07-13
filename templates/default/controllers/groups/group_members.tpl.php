<?php

    $this->setPageTitle(LANG_GROUPS_GROUP_MEMBERS, $group['title']);

    $this->addBreadcrumb(LANG_GROUPS, $this->href_to(''));
    $this->addBreadcrumb($group['title'], $this->href_to($group['id']));
    $this->addBreadcrumb(LANG_GROUPS_GROUP_MEMBERS);

?>

<div id="group_profile_header">
    <?php $this->renderChild('group_header', array('group'=>$group)); ?>
</div>

<div id="user_content_list"><?php echo $profiles_list_html; ?></div>
