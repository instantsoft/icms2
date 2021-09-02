<?php $this->addTplJSNameFromContext([
    'jquery-ui',
    'i18n/jquery-ui/'.cmsCore::getLanguageName()
    ]); ?>
<?php $this->addTplCSSNameFromContext('jquery-ui'); ?>
<?php if($field->title){ ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<div class="form-inline">
<?php echo html_datepicker($field->data['fname_date'], $field->data['date'], ['id'=>$field->id, 'class' => 'mr-sm-2 mb-2 mb-sm-0'], ['minDate'=>date('d.m.Y', 86400)]); ?>
<?php if($field->data['show_time']){ ?>
    <?php echo html_select_range($field->data['fname_hours'], 0, 23, 1, true, $field->data['hours'], ['class' => 'w-auto mr-2']); ?>
    <span class="mr-2">:</span>
    <?php echo html_select_range($field->data['fname_mins'], 0, 59, 1, true, $field->data['mins'], ['class' => 'w-auto mr-2']); ?>
<?php } ?>
    <a class="btn text-decoration-none" onclick="return parser_current_time_<?php echo $field->id; ?>(this);" href="#">
        <span class="d-md-none">
            <?php html_svg_icon('solid', 'user-clock'); ?>
        </span>
        <span class="d-none d-md-inline-block">
            <?php echo LANG_PARSER_CURRENT_TIME; ?>
        </span>
    </a>
</div>
<?php ob_start(); ?>
<script>
    function parser_current_time_<?php echo $field->id; ?>(a){
        var now = new Date();
        var p = $(a).parent();
        p.find('input:eq(0)').val((now.getDate()+'.'+(now.getMonth()+1)+'.'+now.getFullYear()).replace(/(\b\d\b)/g, '0$1'));
        <?php if($field->data['show_time']){ ?>
        p.find('select:eq(0) > option:selected').attr('selected', false);
        p.find('select:eq(0) > option[value='+(now.getHours()+'').replace(/(\b\d\b)/g, '0$1')+']').prop('selected', true);
        p.find('select:eq(1) > option:selected').attr('selected', false);
        var mins = now.getMinutes(), last = p.find('select:eq(1) > option[value=00]');
        p.find('select:eq(1) > option').each(function(){
            if($(this).val()*1 > mins){
                last.prop('selected', true);
                return;
            }
            last = $(this);
        });
        if(last.not(':selected')){
            last.prop('selected', true);
        }
        <?php } ?>
        return false;
    }
</script>
<?php $this->addBottom(ob_get_clean()); ?>
