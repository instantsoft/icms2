<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<?php
$in_filter_as = $field->getOption('in_filter_as');
if($field->context === 'filter' && $in_filter_as && $in_filter_as !== 'input'){ ?>
    <?php if($in_filter_as === 'select'){ ?>
        <?php echo html_select($field->element_name, $field->data['items'], $value, array('id'=>$field->id)); ?>
    <?php }elseif($in_filter_as === 'checkbox'){ ?>
        <?php echo html_checkbox($field->element_name, !empty($value), 1, array('id'=>$field->id)); ?>
    <?php } ?>
<?php }else{ ?>
<?php if(!isset($field->prefix) && !isset($field->suffix)){ ?>
    <?php echo html_input($field->data['type'], $field->element_name, $value, $field->data['attributes']); ?>
<?php } ?>

<?php if(isset($field->prefix) || isset($field->suffix)){ ?>
    <div class="input-prefix-suffix">
        <?php if(isset($field->prefix)) { ?><span class="prefix"><?php echo $field->prefix; ?></span><?php } ?>
        <?php echo html_input($field->data['type'], $field->element_name, $value, $field->data['attributes']); ?>
        <?php if(isset($field->suffix)) { ?><span class="suffix"><?php echo $field->suffix; ?></span><?php } ?>
    </div>
<?php } ?>
<?php if($field->getOption('show_symbol_count')){ ?>
<script type="text/javascript">
$(function(){
    icms.forms.initSymbolCount('<?php echo $field->id; ?>', <?php echo intval($field->getOption('max_length')) ?: 0; ?>, <?php echo intval($field->getOption('min_length')) ?: 0; ?>);
});
</script>
<?php } ?>
<?php if($field->data['autocomplete']){ ?>
    <?php $this->addJSFromContext('templates/default/js/jquery-ui.js'); ?>
    <?php $this->addCSSFromContext('templates/default/css/jquery-ui.css'); ?>

<script type="text/javascript">
    var cache = {};
    <?php if(!empty($field->data['autocomplete']['multiple'])) { ?>
        $( "#<?php echo $field->id; ?>" ).bind('keydown', function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB &&
                $( this ).data('ui-autocomplete').menu.active ) {
                event.preventDefault();
            }
        }).autocomplete({
            minLength: 2,
            source: function( request, response ) {
                var full_term = request.term;
                var term = full_term.split( /,\s*/ ).pop();
                if (term.length < 2) {
                    return false;
                }
                if (term in cache) {
                    response(cache[term]);
                    return;
                }
                $.getJSON( '<?php echo $field->data['autocomplete']['url']; ?>', {
                    term: term
                }, function(data, status, xhr) {
                    cache[term] = data;
                    response(data);
                });
            },
            focus: function() {
                return false;
            },
            select: function( event, ui ) {
                var terms = this.value.split( /,\s*/ );
                terms.pop();
                terms.push( ui.item.value );
                terms.push('');
                this.value = terms.join(', ');
                icms.events.run('autocomplete_select', this);
                return false;
            }
        });
    <?php } else { ?>
        $( "#<?php echo $field->id; ?>" ).autocomplete({
            minLength: 2,
            delay: 500,
            source: function( request, response ) {
                var term = request.term;
                if ( term in cache ) {
                    response( cache[ term ] );
                    return;
                }
                $.getJSON('<?php echo $field->data['autocomplete']['url']; ?>', request, function( data, status, xhr ) {
                    cache[ term ] = data;
                    response( data );
                });
            },
            select: function( event, ui ) {
                icms.events.run('autocomplete_select', this);
            }
        });
    <?php } ?>
</script>
<?php }
}

