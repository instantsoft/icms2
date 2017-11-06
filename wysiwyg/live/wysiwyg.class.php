<?php
class cmsWysiwygLive{

	public function displayEditor($field_id, $content=''){

        $lang = cmsCore::getLanguageName();
        if ($lang == 'en') { $lang = 'en-US'; }

        $template = cmsTemplate::getInstance();
        $user     = cmsUser::getInstance();

        $template->addJSFromContext("wysiwyg/live/scripts/language/{$lang}/editor_lang.js");
        $template->addJSFromContext('wysiwyg/live/scripts/innovaeditor.js');

        $dom_id = str_replace(array('[',']'), array('_', ''), $field_id);

        echo html_textarea($field_id, $content, array('id'=>$dom_id)); ?>

            <div id="innovaajax<?php echo $dom_id; ?>"></div>

            <script type="text/javascript">
                <?php if ($user->is_admin) { ?>
                    groups = [
                        ["group1", "", ["Paragraph", "FontName", "FontSize", "Superscript", "ForeColor", "BackColor", "BRK", "Bold", "Italic", "Underline", "Strikethrough", "TextDialog", "RemoveFormat"]],
                        ["group2", "", ["JustifyLeft", "JustifyCenter", "JustifyRight", "Line", "BRK", "Bullets", "Numbering", "Indent", "Outdent"]],
                        ["group3", "", ["Table","TableDialog", "Emoticons", "BRK", "LinkDialog", "ImageDialog", "YoutubeDialog"]],
                        ["group4", "", ["SearchDialog", "SourceDialog", "CharsDialog", "BRK", "Undo", "Redo", "FullScreen"]]
                    ];
                <?php } else { ?>
                    groups = [
                        ["group1", "", ["Paragraph", "FontName", "FontSize", "Superscript", "ForeColor", "BackColor", "BRK", "Bold", "Italic", "Underline", "Strikethrough", "TextDialog", "RemoveFormat"]],
                        ["group2", "", ["JustifyLeft", "JustifyCenter", "JustifyRight", "Line", "BRK", "Bullets", "Numbering", "Indent", "Outdent"]],
                        ["group3", "", ["Table","TableDialog", "Emoticons", "BRK", "LinkDialog", "ImageDialog", "YoutubeDialog"]],
                        ["group4", "", ["SearchDialog", "CharsDialog", "BRK", "Undo", "Redo", "FullScreen"]]
                    ];
                <?php } ?>
                window["le_<?php echo $dom_id; ?>"] = new InnovaEditor("le_<?php echo $dom_id; ?>");
                window["le_<?php echo $dom_id; ?>"].width = '100%';
                window["le_<?php echo $dom_id; ?>"].height = 290;
                window["le_<?php echo $dom_id; ?>"].css = '<?php echo cmsConfig::get('root') . 'wysiwyg/live/styles/simple.css'; ?>';
                window["le_<?php echo $dom_id; ?>"].enableTableAutoformat = true;
                window["le_<?php echo $dom_id; ?>"].fileBrowser = '/live_editor/upload';
                window["le_<?php echo $dom_id; ?>"].groups = groups;
                window["le_<?php echo $dom_id; ?>"].btnSave=false;
                window["le_<?php echo $dom_id; ?>"].REPLACE('<?php echo $dom_id; ?>', 'innovaajax<?php echo $dom_id; ?>');
            </script>
        <?php
	}

}
