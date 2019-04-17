<?php
class cmsWysiwygAce {

    private static $redactor_loaded = false;

    private $options = [
        'theme' => 'ace/theme/dreamweaver',
        'mode'  => 'ace/mode/html',
        'wrap'  => true,
        'fontSize' => 14,
        'enableBasicAutocompletion' => true,
        'enableSnippets' => true,
        'enableEmmet' => true,
        'showLineNumbers' => true,
        'enableLiveAutocompletion' => true,
        'newLineMode' => 'unix',
        'autoScrollEditorIntoView' => true,
        'minLines' => 20,
        'maxLines' => 40
    ];

    public function __construct($config = []) {
        $this->options = array_replace_recursive($this->options, $config);
    }

	public function displayEditor($field_id, $content = '', $config = []){

        $this->loadRedactor();

        $dom_id = str_replace(array('[',']'), array('_', ''), $field_id);

        if($dom_id){
            echo html_textarea($field_id, $content, array('id' => $dom_id));
        }

        ob_start(); ?>

        <script type="text/javascript">
            <?php if($dom_id){ ?>
                $(function(){
                    init_ace('<?php echo $dom_id; ?>');
                });
            <?php } ?>
            function init_ace (dom_id){
                var textarea = $('#'+dom_id).hide();
                $('<pre class="ace_redactor" id="'+dom_id+'_ace">'+$(textarea).html()+'</pre><div class="scrollmargin"></div>').insertAfter(textarea);
                ace.require('ace/ext/language_tools');
                var editor = ace.edit(dom_id+'_ace', <?php echo json_encode($this->options); ?>);
                editor.getSession().on('changeAnnotation', function() {
                    var annotations = editor.getSession().getAnnotations()||[], i = len = annotations.length;
                    while (i--) {
                        if(/doctype first\. Expected/.test(annotations[i].text)) {
                            annotations.splice(i, 1);
                        }
                    }
                    if(len>annotations.length) {
                        editor.getSession().setAnnotations(annotations);
                    }
                });
                editor.getSession().on('change', function(){
                    textarea.val(editor.getSession().getValue());
                });
                icms.forms.addWysiwygsInsertPool(dom_id, function(field_element, text){
                    editor.session.setValue(text, 1);
                    editor.clearSelection();
                    editor.focus();
                });
                icms.forms.addWysiwygsAddPool(dom_id, function(field_element, text){
                    editor.session.insert(editor.getCursorPosition(), text);
                });
            }
        </script>

       <?php cmsTemplate::getInstance()->addBottom(ob_get_clean());

	}

    private function loadRedactor() {

        if(self::$redactor_loaded){ return false; }

        $template = cmsTemplate::getInstance();

        $template->addJSFromContext('wysiwyg/ace/files/ace.js');
        $template->addJSFromContext('wysiwyg/ace/files/ext-emmet.js');
        $template->addJSFromContext('wysiwyg/ace/files/ext-language_tools.js');

    }

}
