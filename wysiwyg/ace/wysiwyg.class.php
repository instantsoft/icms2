<?php
class cmsWysiwygAce {

	public function displayEditor($field_id, $content = '', $config = array()){

        $dom_id = str_replace(array('[',']'), array('_', ''), $field_id);

        echo html_textarea($field_id, $content, array('id' => $dom_id));

        cmsTemplate::getInstance()->addJSFromContext('wysiwyg/ace/files/ace.js');
        cmsTemplate::getInstance()->addJSFromContext('wysiwyg/ace/files/ext-emmet.js');
        cmsTemplate::getInstance()->addJSFromContext('wysiwyg/ace/files/ext-language_tools.js');

        $options = array();

        $options['theme'] = 'ace/theme/dreamweaver';
        $options['mode'] = 'ace/mode/html';
        $options['wrap'] = true;
        $options['fontSize'] = 14;
        $options['enableBasicAutocompletion'] = true;
        $options['enableSnippets'] = true;
        $options['enableEmmet'] = true;
        $options['enableLiveAutocompletion'] = true;
        $options['newLineMode'] = 'unix';
        $options['autoScrollEditorIntoView'] = true;
        $options['minLines'] = 20;
        $options['maxLines'] = 40;

        $options = array_merge($options, $config);

        ?>

        <pre class="ace_redactor" id="<?php echo $dom_id; ?>_ace"><?php html($content); ?></pre>
        <div class="scrollmargin"></div>

        <script type="text/javascript">
            ace.require("ace/ext/language_tools");
            var editor<?php echo $dom_id; ?> = ace.edit("<?php echo $dom_id; ?>_ace", <?php echo json_encode($options); ?>);
            var textarea<?php echo $dom_id; ?> = $('#<?php echo $dom_id; ?>').hide();
            editor<?php echo $dom_id; ?>.getSession().on("changeAnnotation", function() {
                var annotations = editor<?php echo $dom_id; ?>.getSession().getAnnotations()||[], i = len = annotations.length;
                while (i--) {
                    if(/doctype first\. Expected/.test(annotations[i].text)) {
                        annotations.splice(i, 1);
                    }
                }
                if(len>annotations.length) {
                    editor<?php echo $dom_id; ?>.getSession().setAnnotations(annotations);
                }
            });
            editor<?php echo $dom_id; ?>.getSession().on('change', function(){
                textarea<?php echo $dom_id; ?>.val(editor<?php echo $dom_id; ?>.getSession().getValue());
            });
        </script>

       <?php

	}

}
