<?php

    $this->setPageTitle($group['sub_title'], $group['title']);
    $this->setPageDescription($group['title'].' · '.$group['sub_title']);

    $this->addBreadcrumb(LANG_GROUPS, $this->href_to(''));
    $this->addBreadcrumb($group['title'], $this->href_to($group['id']));
    $this->addBreadcrumb($group['sub_title']);

?>

<div id="group_profile_header">
    <?php $this->renderChild('group_header', array('group'=>$group)); ?>
</div>

<div id="user_content_list"><?php echo $profiles_list_html; ?></div>