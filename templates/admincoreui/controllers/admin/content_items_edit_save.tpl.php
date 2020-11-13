<h3><?php echo LANG_CP_CONTENT_ITEMS_EDIT_S2; ?></h3>
<?php

$this->renderForm($form, [], array(
    'action' => $this->href_to('content', array('items_edit', $ctype['id'])),
    'method' => 'ajax',
    'submit' => array(
        'title' => LANG_SAVE
    )
), $errors);
