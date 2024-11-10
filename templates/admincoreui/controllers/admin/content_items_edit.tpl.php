<h4><?php echo LANG_CP_CONTENT_ITEMS_EDIT_S1; ?></h4>
<?php

$this->renderForm($form, [], [
    'action' => $this->href_to('content', ['items_edit', $ctype['id']]),
    'method' => 'ajax',
    'submit' => [
        'title' => LANG_CONTINUE
    ]
], $errors);
