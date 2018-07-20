<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

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
    icms.forms.initSymbolCount('<?php echo $field->id; ?>', <?php echo ($field->getOption('max_length') ? (int)$field->getOption('max_length') : 0) ?>, <?php echo ($field->getOption('min_length') ? (int)$field->getOption('min_length') : 0) ?>);
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
                /* Массив тегов из строки */
                var terms = full_term.split( /,\s*/ );
                /* Позиция курсора в строке */
                var cursor_pos = getCursorPosition( document.getElementById( "<?php echo $field->id; ?>" ) );
                /* Номер редактируемого тега */
                var term_num = full_term.substring( 0, cursor_pos ).split( /,\s*/ ).length;
                /* Сам редактируемый тег */
                var term = terms[term_num - 1];
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
                /* Массив тегов из строки */
                var terms = this.value.split( /,\s*/ );
                /* Общее количество тегов в строке */
                var terms_count = terms.length;
                /* Позиция курсора в строке */
                var cursor_pos = getCursorPosition( document.getElementById( "<?php echo $field->id; ?>" ) );
                /* Номер редактируемого тега */
                var term_num = this.value.substring( 0, cursor_pos ).split( /,\s*/ ).length;
                /* Признак, что редактируемый тег последний в строке */
                var is_last = (term_num === terms_count);
                /* Подставляем выбранный из автоподбора тег */
                terms[term_num-1] = ui.item.value;
                /* Формируем новую строку с тегами */
                var full_term = '';
                var left_term_new = '';
                var i;
                for (i = 0; i < terms_count; i++) {
                    if (i < terms_count - 1) {
                        /* Объединяем все теги, кроме последнего */
                        full_term += terms[i] + ', ';
                    } else {
                        /* Последний добавляем только если он не пустой */
                        if (terms[i].length > 0) full_term += terms[i] + ', ';
                    }
                    /* Получаем строку тегов до редактируемого включительно */
                    if (i < term_num) left_term_new += terms[i] + ', ';
                }
                this.value = full_term;
                /* Новая позиция курсора */
                cursor_pos = left_term_new.length;
                if (!is_last) cursor_pos -= 2;
                setCursorPosition( document.getElementById( "<?php echo $field->id; ?>" ), cursor_pos );
                icms.events.run('autocomplete_select', this);
                return false;
            }
        });
        function getCursorPosition( ctrl ) {
            var CaretPos = 0;
            /* IE < 9 Support */
            if ( document.selection ) {
                ctrl.focus ();
                var Sel = document.selection.createRange();
                Sel.moveStart ('character', -ctrl.value.length);
                CaretPos = Sel.text.length;
            /* IE >=9 and other browsers */
            } else if ( ctrl.selectionStart || ctrl.selectionStart == '0' ) {
                CaretPos = ctrl.selectionStart;
            }
            return CaretPos;
        };
        function setCursorPosition( ctrl, pos ) {
            /* IE >=9 and other browsers */
            if(ctrl.setSelectionRange) {
                ctrl.focus();
                ctrl.setSelectionRange(pos, pos);
            }
            /* IE < 9 Support */
            else if (ctrl.createTextRange) {
                var range = ctrl.createTextRange();
                range.collapse(true);
                range.moveEnd('character', pos);
                range.moveStart('character', pos);
                range.select();
            }
        };
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
