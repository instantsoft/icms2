<div class="billing-deposit-modal">
    <?php
    $this->renderForm($form, $options, [
        'action'  => href_to('billing', 'add_balance', [$user_id, 1]),
        'method'  => 'ajax',
        'toolbar' => false,
        'submit'  => [
            'title' => LANG_CONTINUE
        ]
    ], $errors);
    ?>
</div>