<h4 class="mb-3"><?php echo LANG_CP_CONTENT_ITEMS_EDIT_S2; ?></h4>
<?php

$this->renderForm($form, [], [
    'action' => $this->href_to('content', ['items_edit', $ctype['id']]),
    'method' => 'ajax',
    'submit' => [
        'title' => LANG_SAVE
    ]
], $errors);
