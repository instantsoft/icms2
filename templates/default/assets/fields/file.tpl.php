<?php $this->addTplJSNameFromContext('files'); ?>

<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<?php if ($value){ ?>
    <?php $file = is_array($value) ? $value : cmsModel::yamlToArray($value); ?>
    <div id="file_<?php echo $field->element_name; ?>" class="value">
        <span class="name">
            <a href="<?php echo $field->getDownloadURL($file); ?>"><?php echo $file['name']; ?></a>
        </span>
        <span class="size"><?php echo files_format_bytes($file['size']); ?></span>
        <a class="ajaxlink delete" href="javascript:" onclick="icms.files.remove('<?php echo $field->element_name; ?>')"><?php echo LANG_DELETE; ?></a>
    </div>
<?php } ?>

<div id="file_<?php echo $field->element_name; ?>_upload" <?php if ($value) { ?>style="display:none"<?php } ?>>
    <?php echo html_file_input($field->element_name); ?>
    <?php echo html_input('hidden', $field->element_name, ''); ?>
    <?php if($field->data['allowed_extensions']){ ?>
        <div class="hint"><?php printf(LANG_PARSER_FILE_EXTS_FIELD_HINT, implode(', ', array_map(function($val) { return trim($val); }, explode(',', mb_strtoupper($field->data['allowed_extensions']))))); ?></div>
    <?php } ?>
    <?php if($field->data['max_size_mb']){ ?>
        <div class="hint"><?php printf(LANG_PARSER_FILE_SIZE_FIELD_HINT, files_format_bytes($field->data['max_size_mb'])); ?></div>
    <?php } ?>
</div>

