<?php

    $this->renderForm($form, [
        'items'   => implode(',', $items),
        'menu_id' => $menu_id
    ], [
        'action' => $this->href_to('menu', ['item_move']),
        'method' => 'ajax'
    ], $errors);
