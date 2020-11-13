<?php $this->addTplJSNameFromContext([
    'jquery-ui',
    'i18n/jquery-ui/'.cmsCore::getLanguageName()
]); ?>
<?php $this->addTplCSSNameFromContext('jquery-ui'); ?>
<?php if ($field->title) { ?><label class="d-block" for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<div class="d-flex align-items-center">
    <div class="input-group mr-3">
        <div class="input-group-prepend">
            <span class="input-group-text"><?php echo LANG_FROM; ?></span>
        </div>
        <?php echo html_datepicker($field->element_name.'[from]', $from, array('id' => $field->id.'_from')); ?>
    </div>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text"><?php echo LANG_TO; ?></span>
        </div>
        <?php echo html_datepicker($field->element_name.'[to]', $to, array('id' => $field->id.'_to')); ?>
    </div>
</div>