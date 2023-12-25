<?php
class cmsWysiwygRedactor {

    private static $redactor_loaded = false;

    private $options = [
        'minHeight' => 200,
        'toolbarFixedBox' => false,
        'plugins' => []
    ];

    private $lang = 'en';

    public function __construct($config = []) {

        $user = cmsUser::getInstance();
        $core = cmsCore::getInstance();
        $this->lang = cmsCore::getLanguageName();

        if($this->lang !== 'en'){
            $this->options['lang'] = $this->lang;
        }

        $this->options['smilesUrl'] = href_to('typograph', 'get_smiles');

        if (!$user->is_admin) {
            $this->options['buttonSource'] = false;
        }

        if ($core->request->isTypeModal()) {
            $this->options['toolbarFixedTarget'] = '#icms_modal';
        }

        if ($user->is_logged) {

            if(empty($config['upload_params'])){

                $context = $core->getUriData();
                $upload_params = [];

                if($context['controller']){
                    $upload_params['target_controller'] = $context['controller'];
                }

                if($context['action']){
                    $upload_params['target_subject'] = mb_substr($context['action'], 0, 32);
                }

                if(strpos($core->uri, '/add/') === false && !empty($context['params'][1]) && is_numeric($context['params'][1])){
                    $upload_params['target_id'] = $context['params'][1];
                }
            } else {

                $upload_params = $config['upload_params'];

                unset($config['upload_params']);
            }

            $upload_params_string = ($upload_params ? '?'.http_build_query($upload_params) : '');

            $this->options['imageUpload'] = href_to('images', 'upload_with_preset', ['file', 'wysiwyg_redactor']).$upload_params_string;

            $this->options['imageGetJson'] = href_to('files', 'files_list', ['image']).$upload_params_string;

            if(!empty($upload_params['target_controller']) && !empty($upload_params['target_subject'])){
                $this->options['predefinedLinks'] = href_to('wysiwygs', 'links_list').$upload_params_string;
            }

        }

        $this->options = array_replace_recursive($this->options, $config);

    }

	public function displayEditor($field_name, $content = '', $config = []){

        $this->loadRedactor();

        $dom_id = isset($this->options['id']) ? $this->options['id'] : 'wysiwyg-' . uniqid(); unset($this->options['id']);

        if($field_name && $dom_id){
            if(!empty($this->options['wysiwyg_toolbar'])){
                echo '<div data-field_id="'.$dom_id.'" id="wysiwyg_toolbar_'.$dom_id.'" class="wysiwyg_toolbar_wrap">'.$this->options['wysiwyg_toolbar'].'</div>';
                unset($this->options['wysiwyg_toolbar']);
            }
            echo html_textarea($field_name, $content, ['id' => $dom_id, 'class' => 'imperavi_redactor']);
        }

        ob_start(); ?>

        <script>
            <?php if($dom_id){ ?>
                redactor_global_options['field_<?php echo $dom_id; ?>'] = <?php echo json_encode($this->options); ?>;
                $(function(){
                    init_redactor('<?php echo $dom_id; ?>');
                });
            <?php } else { ?>
                redactor_global_options['default'] = <?php echo json_encode($this->options); ?>;
            <?php } ?>
        </script>

        <?php cmsTemplate::getInstance()->addBottom(ob_get_clean());

	}

    private function loadRedactor() {

        $template = cmsTemplate::getInstance();

        if (!empty($this->options['plugins'])) {

            foreach ($this->options['plugins'] as $plugin) {
                $template->addJSFromContext('wysiwyg/redactor/files/plugins/' . $plugin . '/' . $plugin . '.js');
            }

            if (in_array('clips', $this->options['plugins'])) {
                $this->options['clipsUrl'] = href_to('wysiwyg/redactor/files/plugins/clips/index.html');
            }
        }

        if (self::$redactor_loaded) {
            return false;
        }

        $template->addJSFromContext('wysiwyg/redactor/files/htmlsanitizer.js');
        $template->addJSFromContext('wysiwyg/redactor/files/redactor.min.js');
        $template->addTplJSNameFromContext('files');

        $css_file     = 'wysiwyg/redactor/files/redactor.css';
        $tpl_css_file = $template->getTplFilePath('css/wysiwyg/redactor/styles.css', false);
        if ($tpl_css_file) {
            $css_file = $tpl_css_file;
        }
        $template->addCSSFromContext($css_file);

        if ($this->lang !== 'en') {
            $template->addJSFromContext('wysiwyg/redactor/files/lang/' . $this->lang . '.js');
        }

        ob_start();
        ?>
            <script>
                var redactor_global_options = {};
                function init_redactor (dom_id){
                    var imperavi_options = {};
                    if(redactor_global_options.hasOwnProperty('field_'+dom_id)){
                        imperavi_options = redactor_global_options['field_'+dom_id];
                    } else if(redactor_global_options.hasOwnProperty('default')) {
                        imperavi_options = redactor_global_options.default;
                    }
                    icms.files.url_delete = '<?php echo href_to('files', 'delete'); ?>';
                    imperavi_options.imageDeleteCallback = function (element){
                        if(confirm('<?php echo LANG_PARSER_IMAGE_DELETE; ?>')){
                            icms.files.deleteByPath($(element).attr('src'));
                        }
                    };
                    $('#'+dom_id).redactor(imperavi_options);
                    icms.forms.addWysiwygsInsertPool(dom_id, function(field_element, text){
                        $('#'+field_element).redactor('set', text);
                        $('#'+field_element).redactor('focus');
                    });
                    icms.forms.addWysiwygsAddPool(dom_id, function(field_element, text){
                        $('#'+field_element).redactor('insertHtml', text);
                    });
                }
            </script>
        <?php

        $template->addBottom(ob_get_clean());

        self::$redactor_loaded = true;
    }

}
