<?php if(empty($modal_btn['is_show'])){ ?>
<?php
    $this->addTplJSName([
        'forms-constructor'
    ]);
?>
<div class="position-relative icms-forms__wrap<?php if(!empty($modal_btn['is_show'])){ ?> d-none<?php } ?>" id="modal-<?php echo $form_data['params']['form_id']; ?>">
    <?php if($form_data['title'] && !empty($form_data['options']['show_title'])){ ?>
        <h3><?php echo $form_data['title']; ?></h3>
    <?php } ?>
    <?php if($form_data['description']){?>
        <?php echo $form_data['description']; ?>
    <?php } ?>
    <?php $this->renderForm($form, [], $form_data['params'], false); ?>
</div>
<?php } ?>
<?php if(!empty($modal_btn['is_show'])){ ?>
<a class="btn ajax-modal<?php if(!empty($modal_btn['class'])){ ?> <?php echo $modal_btn['class']; ?><?php } ?>" href="<?php echo href_to('forms', 'view', [$form_data['hash']]); ?>" title="<?php html($form_data['title']); ?>">
    <?php if (!empty($modal_btn['icon'])) {
        $icon_params = explode(':', $modal_btn['icon']);
        if(!isset($icon_params[1])){ array_unshift($icon_params, 'solid'); }
        html_svg_icon($icon_params[0], $icon_params[1]);
    } ?>
    <?php echo $modal_btn['title']; ?>
</a>
<?php } ?>