<?php $this->addJS('templates/default/js/files.js'); ?>

<?php
    $allowed_extensions = $field->getOption('extensions');
    $max_size_mb = $field->getOption('max_size_mb');
    if ($max_size_mb){
        $max_size_mb *= 1048576;
    } else {
        $max_size_mb = files_convert_bytes(ini_get('post_max_size'));
    }
?>

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
    <?php if ($allowed_extensions){ ?>
        <div class="hint"><?php printf(LANG_PARSER_FILE_EXTS_FIELD_HINT, implode(', ', array_map(function($val) { return trim($val); }, explode(',', mb_strtoupper($allowed_extensions))))); ?></div>
    <?php } ?>
    <?php if ($max_size_mb){ ?>
        <div class="hint"><?php printf(LANG_PARSER_FILE_SIZE_FIELD_HINT, files_format_bytes($max_size_mb)); ?></div>
    <?php } ?>
</div>

