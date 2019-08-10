<?php $this->addTplJSNameFromContext([
    'jquery-ui',
    'i18n/jquery-ui/'.cmsCore::getLanguageName()
    ]); ?>
<?php $this->addTplCSSNameFromContext('jquery-ui'); ?>

<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->getOption('date_title'); ?></label><?php } ?>

<?php echo html_datepicker($field->element_name, $field->data['date'], array('id'=>$field->id), array('yearRange' => '-120:+0')); ?>
