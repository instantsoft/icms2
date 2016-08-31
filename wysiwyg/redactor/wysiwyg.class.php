<?php
class cmsWysiwygRedactor{

	public function displayEditor($field_id, $content=''){

        $lang = cmsCore::getLanguageName();
		$user = cmsUser::getInstance();

		$template = cmsTemplate::getInstance();

        $template->addCSSFromContext('wysiwyg/redactor/css/redactor.css');
        $template->addJSFromContext('wysiwyg/redactor/js/redactor.js');
        $template->addJSFromContext('wysiwyg/redactor/js/video.js');
        $template->addJSFromContext('wysiwyg/redactor/js/fullscreen.js');
        $template->addJSFromContext('wysiwyg/redactor/js/fontsize.js');
        //$template->addJSFromContext('wysiwyg/redactor/js/fontfamily.js');
        $template->addJSFromContext('wysiwyg/redactor/js/fontcolor.js');
        $template->addJSFromContext('wysiwyg/redactor/js/table.js');
        $template->addJSFromContext("wysiwyg/redactor/langs/{$lang}.js");

        $dom_id = str_replace(array('[',']'), array('_', ''), $field_id);

        echo html_textarea($field_id, $content, array('id'=>$dom_id));

        ?>
            <script type="text/javascript">
                $(function(){
                    $('#<?php echo $dom_id; ?>').redactor({
                        lang: '<?php echo $lang; ?>',
                        plugins: ['video', 'fontsize', 'fontcolor', 'fullscreen', 'table'],
                        imageUpload: '<?php echo href_to('redactor/upload'); ?>',
						minHeight: 190,
                        replaceDivs: false,
                        removeComments: true,
                        convertLinks: false,
                        pastePlainText: true,
						<?php if (!$user->is_admin) { ?>
                            buttonsHide: ['html']
						<?php } ?>
                    });
                    <?php if(!cmsCore::getInstance()->request->isAjax()){ ?>
                        $(window).on('resize', function (){
                            $('#<?php echo $dom_id; ?>').width($('#f_<?php echo $dom_id; ?>').width());
                        }).triggerHandler('resize');
                    <?php } ?>
                });
            </script>
        <?php
	}

}
