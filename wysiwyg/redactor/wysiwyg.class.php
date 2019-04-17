<?php
class cmsWysiwygRedactor {

    private static $redactor_loaded = false;

    private $options = [
        'minHeight' => 200,
        'toolbarFixedBox' => false,
        'plugins' => []
    ];

    public function __construct($config = []) {

        $user = cmsUser::getInstance();
        $core = cmsCore::getInstance();
        $lang = cmsCore::getLanguageName();

        if($lang !== 'en'){
            $this->options['lang'] = $lang;
        }

        $this->options['smilesUrl'] = href_to('typograph', 'get_smiles');

        if (!$user->is_admin) {
            $this->options['buttonSource'] = false;
        }

        if ($user->is_logged) {

            $context = $core->getUriData();
            $upload_params = [];

            if($context['controller']){
                $upload_params['target_controller'] = $context['controller'];
            }

            if($context['action']){
                $upload_params['target_subject'] = $context['action'];
            }

            if(strpos($core->uri, '/add/') === false && !empty($context['params'][1]) && is_numeric($context['params'][1])){
                $upload_params['target_id'] = $context['params'][1];
            }

            $upload_params_string = ($upload_params ? '?'.http_build_query($upload_params) : '');

            $this->options['imageUpload'] = href_to('images', 'upload_with_preset', ['file', 'wysiwyg_redactor']).$upload_params_string;

            $this->options['imageGetJson'] = href_to('files', 'files_list', ['image']).$upload_params_string;

            if($context['controller'] && $context['action']){
                $this->options['predefinedLinks'] = href_to('wysiwygs', 'links_list').$upload_params_string;
            }

        }

        $this->options = array_replace_recursive($this->options, $config);

    }

	public function displayEditor($field_id, $content = '', $config = []){

        $this->loadRedactor();

        $dom_id = str_replace(array('[',']'), array('_', ''), $field_id);

        if($dom_id){
            echo html_textarea($field_id, $content, array('id' => $dom_id, 'class' => 'imperavi_redactor'));
        }

        ob_start(); ?>

        <script type="text/javascript">
            <?php if($dom_id){ ?>
                $(function(){
                    init_redactor('<?php echo $dom_id; ?>');
                });
            <?php } ?>
            function init_redactor (dom_id){
                var imperavi_options = <?php echo json_encode($this->options); ?>;
                icms.files.url_delete = '<?php echo href_to('files', 'delete'); ?>';
                imperavi_options.imageDeleteCallback = function (element){
                    if(confirm('<?php echo LANG_PARSER_IMAGE_DELETE; ?>')){
                        icms.files.deleteByPath($(element).attr('src'));
                    }
                };
                $('#'+dom_id).redactor(imperavi_options);
                icms.forms.addWysiwygsInsertPool(dom_id, function(field_element, text){
                    $(field_element).redactor('set', text);
                    $(field_element).redactor('focus');
                });
                icms.forms.addWysiwygsAddPool(dom_id, function(field_element, text){
                    $(field_element).redactor('insertText', text);
                });
            }
        </script>

        <?php cmsTemplate::getInstance()->addBottom(ob_get_clean());

	}

    private function loadRedactor() {

        if(self::$redactor_loaded){ return false; }

        $template = cmsTemplate::getInstance();

        $template->addJSFromContext('wysiwyg/redactor/files/redactor.js');
        $template->addCSSFromContext('wysiwyg/redactor/files/redactor.css');
        $template->addJSFromContext('templates/default/js/files.js');

        if(!empty($this->options['plugins'])){
            foreach($this->options['plugins'] as $plugin){
                $template->addJSFromContext('wysiwyg/redactor/files/plugins/'.$plugin.'/'.$plugin.'.js');
            }
        }

        if($this->options['lang'] !== 'en'){
            $template->addJSFromContext('wysiwyg/redactor/files/lang/'.$this->options['lang'].'.js');
        }

        self::$redactor_loaded = true;

    }

}
