<?php $this->addTplJSNameFromContext([
    'jquery-ui',
    'i18n/jquery-ui/'.cmsCore::getLanguageName()
    ]); ?>
<?php $this->addTplCSSNameFromContext('jquery-ui'); ?>

<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<?php echo LANG_FROM . ' ' . html_datepicker($field->element_name.'[from]', $from, array('id' => $field->id.'_from')).' '; ?>
<?php echo LANG_TO . ' ' . html_datepicker($field->element_name.'[to]', $to, array('id' => $field->id.'_to')); ?>
