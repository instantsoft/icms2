<div class="modal_padding">
    <?php
        $this->renderForm($form, $values, array(
            'action' => $this->href_to('index_page_settings'),
            'method' => 'post',
            'submit' => array(
                'title' => LANG_APPLY
            )
        ), $errors);
    ?>
</div>