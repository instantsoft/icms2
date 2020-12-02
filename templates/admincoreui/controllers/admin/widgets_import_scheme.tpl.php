<?php
    $this->renderForm($form, $data, array(
        'action' => $this->href_to('widgets', ['import_scheme', $template_name]),
        'method' => 'ajax'
    ), $errors);
