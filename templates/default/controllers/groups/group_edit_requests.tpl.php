<?php $this->addTplJSName('groups'); ?>
<h1><?php echo LANG_GROUPS_REQUESTS ?></h1>

<?php $this->renderChild('group_edit_header', array('group' => $group)); ?>

<div id="user_content_list"><?php echo $profiles_list_html; ?></div>