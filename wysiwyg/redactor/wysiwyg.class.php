<?php
class cmsWysiwygRedactor {

    private static $redactor_loaded = false;

	public function displayEditor($field_id, $content=''){

        $this->loadRedactor();

        $dom_id = str_replace(array('[',']'), array('_', ''), $field_id);

        echo html_textarea($field_id, $content, array('id' => $dom_id, 'class' => 'imperavi_redactor'));

	}

    private function loadRedactor() {

        if(self::$redactor_loaded){ return false; }

        $lang = cmsCore::getLanguageName();

        $template = cmsTemplate::getInstance();
        $user = cmsUser::getInstance();
        $core = cmsCore::getInstance();

        $template->addJSFromContext('wysiwyg/redactor/files/redactor.js');
        $template->addCSSFromContext('wysiwyg/redactor/files/redactor.css');
        $template->addJSFromContext('templates/default/js/files.js');

        $options = array();

        $plugins = array(
            'fontfamily',
            'fontsize',
            'fullscreen',
            'fontcolor'
        );

        foreach($plugins as $plugin){

            $options['plugins'][] = $plugin;

            $template->addJSFromContext('wysiwyg/redactor/files/plugins/'.$plugin.'/'.$plugin.'.js');

        }

        if($lang !== 'en'){

            $template->addJSFromContext('wysiwyg/redactor/files/lang/'.$lang.'.js');

            $options['lang'] = $lang;

        }

        //конвертирование ссылок vimeo и youtube
        $options['convertVideoLinks'] = true;

        //авторесайз поля вводя
        $options['autoresize'] = true;

        //отмена конвертирования дивов в параграфы
        $options['convertDivs'] = false;

        // прилипание тулбара
        $options['toolbarFixed'] = true;

        if (!$user->is_admin) {
            $options['buttonSource'] = false;
        }

        if ($user->is_logged) {

            $context = $core->getUriData();
            $upload_params = array();

            if($context['controller']){
                $upload_params['target_controller'] = $context['controller'];
            }

            if($context['action']){
                $upload_params['target_subject'] = $context['action'];
            }

            if(strpos($core->uri, '/add/') === false && !empty($context['params'][0]) && is_numeric($context['params'][0])){
                $upload_params['target_id'] = $context['params'][0];
            }

            $options['imageUpload'] = href_to('redactor/upload').($upload_params ? '?'.http_build_query($upload_params) : '');

            $options['imageGetJson'] = href_to('redactor/images_list').($upload_params ? '?'.http_build_query($upload_params) : '');

            if($context['controller'] && $context['action']){
                $options['predefinedLinks'] = href_to('redactor/links_list').($upload_params ? '?'.http_build_query($upload_params) : '');
            }

        }

        $options['minHeight'] = 200;

        ?>

        <script type="text/javascript">
            $(function(){
                var imperavi_options = <?php echo json_encode($options); ?>;
                icms.files.url_delete = '<?php echo href_to('files', 'delete'); ?>';
                imperavi_options.imageDeleteCallback = function (element){
                    if(confirm('<?php echo LANG_PARSER_IMAGE_DELETE; ?>')){
                        icms.files.deleteByPath($(element).attr('src'));
                    }
                };
                $('.imperavi_redactor').redactor(imperavi_options);
            });
        </script>

       <?php

       self::$redactor_loaded = true;

    }

}
