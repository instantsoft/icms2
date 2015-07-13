<?php

    $this->setPageTitle($group['title']);

    $this->addBreadcrumb(LANG_GROUPS, href_to('groups'));
    $this->addBreadcrumb($group['title']);

?>

<div id="group_profile_header">
    <?php $this->renderChild('group_header', array('group'=>$group)); ?>
</div>

<div id="group_profile">

    <div id="left_column" class="column">

        <div id="logo" class="block">
            <?php echo html_image($group['logo'], 'normal'); ?>
        </div>

    </div>

    <div id="right_column" class="column">

        <div id="information" class="content_item block">

            <div class="group_description">
                <?php echo LANG_GROUP_IS_CLOSED; ?>
            </div>

        </div>

    </div>

</div>
