<?php
class cmsWysiwygLive{

	function __construct(){

	}

	public function displayEditor($field_id, $content=''){

        $lang = cmsConfig::get('language');

        if ($lang == 'en') { $lang = 'en-US'; }

        cmsTemplate::getInstance()->addJS("wysiwyg/live/scripts/language/{$lang}/editor_lang.js", 'LiveEditor Lang', false);
        cmsTemplate::getInstance()->addJS('wysiwyg/live/scripts/innovaeditor.js', 'LiveEditor', false);

        $dom_id = str_replace(array('[',']'), array('_', ''), $field_id);

        echo html_textarea($field_id, $content, array('id'=>$dom_id));

        ?>
            <script type="text/javascript">
                var le_<?php echo $dom_id; ?> = new InnovaEditor("le_<?php echo $dom_id; ?>");
                le_<?php echo $dom_id; ?>.width = '100%';
                le_<?php echo $dom_id; ?>.height = 350;
                le_<?php echo $dom_id; ?>.css = '<?php echo cmsConfig::get('root') . 'wysiwyg/live/styles/simple.css'; ?>';
                le_<?php echo $dom_id; ?>.enableTableAutoformat = true;
                le_<?php echo $dom_id; ?>.fileBrowser = '/live_editor/upload';
                le_<?php echo $dom_id; ?>.groups = [
                    ["group1", "", ["Paragraph", "FontName", "FontSize", "Superscript", "ForeColor", "BackColor", "BRK", "Bold", "Italic", "Underline", "Strikethrough", "TextDialog", "RemoveFormat"]],
                    ["group2", "", ["JustifyLeft", "JustifyCenter", "JustifyRight", "Line", "BRK", "Bullets", "Numbering", "Indent", "Outdent"]],
                    ["group3", "", ["Table","TableDialog", "Emoticons", "BRK", "LinkDialog", "ImageDialog", "YoutubeDialog"]],
                    ["group4", "", ["SearchDialog", "SourceDialog", "CharsDialog", "BRK", "Undo", "Redo", "FullScreen"]]
                    ];
                le_<?php echo $dom_id; ?>.REPLACE('<?php echo $dom_id; ?>');
            </script>
        <?php
	}

}
