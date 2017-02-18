<?php

    $this->setPageTitle($group['sub_title'], $group['title']);
    $this->setPageDescription($group['title'].' · '.$group['sub_title']);

    $this->addBreadcrumb(LANG_GROUPS, href_to('groups'));
    $this->addBreadcrumb($group['title'], $this->href_to($group['id']));
    $this->addBreadcrumb($group['sub_title']);

?>

<div id="group_profile_header">
    <?php $this->renderChild('group_header', array('group'=>$group)); ?>
</div>

<?php echo $html;
