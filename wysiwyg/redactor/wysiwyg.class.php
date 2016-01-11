<?php
class cmsWysiwygRedactor{

	public function displayEditor($field_id, $content=''){

        $lang = cmsConfig::get('language');
		$user = cmsUser::getInstance();

		$template = cmsTemplate::getInstance();

        $template->addCSSFromContext('wysiwyg/redactor/css/redactor.css');
        $template->addJSFromContext('wysiwyg/redactor/js/redactor.js');
        $template->addJSFromContext('wysiwyg/redactor/js/video.js');
        $template->addJSFromContext('wysiwyg/redactor/js/fullscreen.js');
        $template->addJSFromContext('wysiwyg/redactor/js/fontsize.js');
        $template->addJSFromContext('wysiwyg/redactor/js/fontfamily.js');
        $template->addJSFromContext('wysiwyg/redactor/js/fontcolor.js');
        $template->addJSFromContext("wysiwyg/redactor/lang/{$lang}.js");

        $dom_id = str_replace(array('[',']'), array('_', ''), $field_id);

        echo html_textarea($field_id, $content, array('id'=>$dom_id));

        ?>

            <script type="text/javascript">
                $(document).ready(function(){
                    $('#<?php echo $dom_id; ?>').redactor({
                        lang: '<?php echo $lang; ?>',
                        plugins: ['video', 'fontfamily', 'fontsize', 'fontcolor', 'fullscreen'],
                        imageUpload: '<?php echo href_to('redactor/upload'); ?>',
						minHeight: 250,
						<?php if ($user->is_admin) { ?>
                            buttonSource: true
						<?php } ?>
                    });
                });
            </script>

        <?php
	}

}
