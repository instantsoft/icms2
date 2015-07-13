<?php
class cmsWysiwygRedactor{

	public function displayEditor($field_id, $content=''){

        $lang = cmsConfig::get('language');
		$user = cmsUser::getInstance();

		$template = cmsTemplate::getInstance();
		
        $template->insertCSS('wysiwyg/redactor/css/redactor.css');
        $template->addJS('wysiwyg/redactor/js/redactor.js');
        $template->addJS('wysiwyg/redactor/js/video.js');
        $template->addJS('wysiwyg/redactor/js/fullscreen.js');
        $template->addJS('wysiwyg/redactor/js/fontsize.js');
        $template->addJS('wysiwyg/redactor/js/fontfamily.js');
        $template->addJS('wysiwyg/redactor/js/fontcolor.js');
        $template->addJS("wysiwyg/redactor/lang/{$lang}.js");

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
