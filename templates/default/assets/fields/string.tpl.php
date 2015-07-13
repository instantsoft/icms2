<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<?php $type = $field->getProperty('is_password') ? 'password' : 'text'; ?>

<?php if(!isset($field->prefix) && !isset($field->suffix)){ ?>
    <?php echo html_input($type, $field->element_name, $value, array('id'=>$field->id)); ?>
<?php } ?>

<?php if(isset($field->prefix) || isset($field->suffix)){ ?>
    <div class="input-prefix-suffix">
        <?php if(isset($field->prefix)) { ?><span class="prefix"><?php echo $field->prefix; ?></span><?php } ?>
        <?php echo html_input($type, $field->element_name, $value, array('id'=>$field->id)); ?>
        <?php if(isset($field->suffix)) { ?><span class="suffix"><?php echo $field->suffix; ?></span><?php } ?>
    </div>
<?php } ?>

<?php $autocomplete = $field->getProperty('autocomplete'); ?>
<?php if ($autocomplete){ ?>
<?php $this->addJS('templates/default/js/jquery-ui.js'); ?>
<?php $this->addCSS('templates/default/css/jquery-ui.css'); ?>

<script>
    var cache = {};

    <?php if (!empty($autocomplete['multiple'])) { ?>
        $( "#<?php echo $field->id; ?>" ).bind( "keydown", function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB &&
                $( this ).data( "ui-autocomplete" ).menu.active ) {
                event.preventDefault();
            }
        }).autocomplete({
            minLength: 2,
            source: function( request, response ) {
                var term = request.term;
                if ( term in cache ) {
                    response( cache[ term ] );
                    return;
                }
                $.getJSON( '<?php echo $autocomplete['url']; ?>', {
                    term: term.split( /,\s*/ ).pop()
                }, function( data, status, xhr ) {
                    cache[ term ] = data;
                    response( data );
                });
            },
            focus: function() {
                return false;
            },
            select: function( event, ui ) {
                var terms = this.value.split( /,\s*/ );
                terms.pop();
                terms.push( ui.item.value );
                terms.push( "" );
                this.value = terms.join( ", " );
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
                $.getJSON('<?php echo $autocomplete['url']; ?>', request, function( data, status, xhr ) {
                    cache[ term ] = data;
                    response( data );
                });
            }
        });
    <?php } ?>
</script>
<?php } ?>
