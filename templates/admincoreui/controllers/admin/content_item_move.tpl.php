<?php

    $this->renderForm($form, [
        'ctype_name'=>$ctype['name'],
        'items' => implode(',', $items)
    ], [
        'action' => $this->href_to('content', ['item_move', $ctype['id'], $parent_id]),
        'method' => 'ajax'
    ], $errors);
