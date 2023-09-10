<?php $this->addTplJSNameFromContext('files'); ?>
<?php if ($field->title) { ?><label><?php echo $field->title; ?></label><?php } ?>

<?php if ($value){ ?>
    <?php $file = is_array($value) ? $value : cmsModel::yamlToArray($value); ?>
    <div id="file_<?php echo $field->element_name; ?>" class="value">
        <span class="name h5">
            <a href="<?php echo $field->getDownloadURL($file); ?>"><?php echo $file['name']; ?></a>
        </span>
        <span class="size mx-2"><?php echo files_format_bytes($file['size']); ?></span>
        <a class="btn btn-sm btn-danger delete" href="javascript:" onclick="icms.files.remove('<?php echo $field->element_name; ?>')" title="<?php echo LANG_DELETE; ?>" data-toggle="tooltip" data-placement="right">
            <?php html_svg_icon('solid', 'minus-circle'); ?>
        </a>
    </div>
<?php } ?>

<div id="file_<?php echo $field->element_name; ?>_upload" <?php if ($value) { ?>style="display:none"<?php } ?>>
    <div class="input-group mb-1">
        <div class="custom-file">
            <?php echo html_file_input($field->element_name, $field->data['attributes']); ?>
            <label class="custom-file-label" for="<?php echo $field->id; ?>" data-browse="<?php echo LANG_SELECT; ?>"><?php echo LANG_PARSER_FILE; ?></label>
        </div>
    </div>
    <?php echo html_input('hidden', $field->element_name, ''); ?>
    <?php if($field->data['allowed_extensions']){ ?>
        <div class="hint text-muted small"><?php printf(LANG_PARSER_FILE_EXTS_FIELD_HINT, implode(', ', array_map(function($val) { return trim($val); }, explode(',', mb_strtoupper($field->data['allowed_extensions']))))); ?></div>
    <?php } ?>
    <?php if($field->data['max_size_mb']){ ?>
        <div class="hint text-muted small"><?php printf(LANG_PARSER_FILE_SIZE_FIELD_HINT, files_format_bytes($field->data['max_size_mb'])); ?></div>
    <?php } ?>
</div>

<?php ob_start(); ?>
<script>
    $(function(){
        $('#<?php echo $field->id; ?>').on('change',function(){
            $(this).next('.custom-file-label').text($(this).val().replace('C:\\fakepath\\', ''));
        });
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>