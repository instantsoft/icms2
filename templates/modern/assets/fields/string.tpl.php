<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php
$in_filter_as = $field->getOption('in_filter_as');
if($field->context === 'filter' && $in_filter_as && $in_filter_as !== 'input'){ ?>

    <?php if($in_filter_as === 'select') { ?>
        <?php echo html_select($field->element_name, $field->data['items'], $value, ['id'=>$field->id]); ?>
    <?php } elseif($in_filter_as === 'checkbox') { ?>
        <?php echo html_checkbox($field->element_name, !empty($value), 1, ['id'=>$field->id]); ?>
    <?php } ?>

<?php } else { ?>

    <?php if(!isset($field->prefix) && !isset($field->suffix)){ ?>
        <?php echo html_input($field->data['type'], $field->element_name, $value, $field->data['attributes']); ?>
    <?php } ?>

    <?php if(isset($field->prefix) || isset($field->suffix)){ ?>
        <div class="input-group input-prefix-suffix">
            <?php if(isset($field->prefix)) { ?>
            <div class="input-group-prepend">
                <span class="prefix input-group-text"><?php echo $field->prefix; ?></span>
            </div>
            <?php } ?>
            <?php echo html_input($field->data['type'], $field->element_name, $value, $field->data['attributes']); ?>
            <?php if(isset($field->suffix)) { ?>
            <div class="input-group-append">
                <span class="suffix input-group-text"><?php echo $field->suffix; ?></span>
            </div>
            <?php } ?>
        </div>
    <?php } ?>
    <?php if($field->getOption('use_inputmask') && $field->getOption('inputmask_str')){ ob_start(); ?>
        <?php $this->addTplJSNameFromContext('vendors/jquery.inputmask.min'); ?>
        <script>
        $(function(){
            $('#<?php echo $field->id; ?>').inputmask("<?php html($field->getOption('inputmask_str')); ?>");
        });
        </script>
    <?php $this->addBottom(ob_get_clean()); } ?>
    <?php if($field->getOption('show_symbol_count')){ ob_start(); ?>
        <script>
        $(function(){
            icms.forms.initSymbolCount('<?php echo $field->id; ?>', <?php echo intval($field->getOption('max_length', 0)); ?>, <?php echo intval($field->getOption('min_length', 0)); ?>);
        });
        </script>
    <?php $this->addBottom(ob_get_clean()); } ?>
    <?php if($field->data['autocomplete']){
            $this->addTplJSNameFromContext('jquery-ui');
            $this->addTplCSSNameFromContext('jquery-ui');

            ob_start(); ?>
        <script>
            initAutocomplete('<?php echo $field->id; ?>', <?php echo (!empty($field->data['autocomplete']['multiple']) ? 'true' : 'false') ?>, '<?php echo $field->data['autocomplete']['url']; ?>', <?php echo (!empty($field->data['autocomplete']['data']) ? json_encode($field->data['autocomplete']['data']) : 'false') ?>, '<?php echo $field->data['autocomplete']['multiple_separator'] ?>');
        </script>
        <?php $this->addBottom(ob_get_clean()); ?>
    <?php } ?>
    <?php if($field->data['type'] === 'password'){
            ob_start(); ?>
        <script>
            $(function(){
                $('#<?php echo $field->id; ?>').wrap("<div class='icms-form__password-field position-relative'></div>");
                $('#f_<?php echo $field->id; ?> .icms-form__password-field').append('<a tabindex="1" href="#" class="icms-form__password-field-icon"><?php html_svg_icon('solid', 'eye'); ?></a>');
                let password_field_icon = $('#f_<?php echo $field->id; ?> .icms-form__password-field-icon');
                $(password_field_icon).on('click', function (){
                    $(this).toggleClass('active');
                    let input = $(this).closest('.icms-form__password-field').find('input');
                    let curtype = input.attr('type');
                    input.attr('type', curtype === 'text' ? 'password' : 'text');
                    return false;
                });
            });
        </script>
        <?php $this->addBottom(ob_get_clean()); ?>
    <?php } ?>

<?php } ?>