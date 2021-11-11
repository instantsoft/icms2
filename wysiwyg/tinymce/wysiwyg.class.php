<?php
class cmsWysiwygTinymce {

    private static $redactor_loaded = false;

    private $buttons = [
        '|','bold','italic','underline','strikethrough','alignleft','aligncenter','alignright','alignjustify','alignnone','styleselect','formatselect','fontselect','fontsizeselect','cut','copy','paste','outdent','indent','blockquote','undo','redo','removeformat','subscript','superscript','visualaid','insert'
    ];

    private $block_formats = [
        'p' => LANG_TINYMCE_P,
        'h2' => LANG_TITLE.' 2',
        'h3' => LANG_TITLE.' 3',
        'h4' => LANG_TITLE.' 4',
        'h5' => LANG_TITLE.' 5',
        'h6' => LANG_TITLE.' 6'
    ];

    private $quickbars_selection_buttons = [
        'bold', 'italic', 'underline', '|', 'quicklink', 'h2', 'h3', 'blockquote'
    ];

    private $quickbars_insert_buttons = [
        'quickimage', 'quicktable'
    ];

    private $buttons_mapping = [
        'wordcount','toc','nonbreaking','media','insertdatetime','image','hr','fullscreen','code',
        'charmap','anchor','smiles', 'emoticons',
        'codesample' => [
            'codesample'
        ],
        'icmsspoiler' => [
            'spoiler-add'
        ],
        'lists' => [
            'bullist', 'numlist'
        ],
        'link' => [
            'link', 'unlink', 'openlink'
        ],
        'table' => [
            'table', 'tabledelete', 'tablecellprops', 'tablemergecells', 'tablesplitcells', 'tableinsertrowbefore',
            'tableinsertrowafter', 'tabledeleterow', 'tablerowprops', 'tablecutrow', 'tablecopyrow', 'tablepasterowbefore',
            'tablepasterowafter', 'tableinsertcolbefore', 'tableinsertcolafter', 'tabledeletecol'
        ]
    ];

    private $options = [
        'plugins' => [
            'autoresize'
        ],
        'textpattern_patterns' => [
            ['start' => '> ', 'format' => 'blockquote']
        ],
        'codesample_languages' => [
            ['text' => 'HTML/XML', 'value' => 'html'],
            ['text' => 'PHP', 'value' => 'php'],
            ['text' => 'JavaScript', 'value' => 'javascript'],
            ['text' => 'CSS', 'value' => 'css'],
            ['text' => 'SQL', 'value' => 'sql'],
            ['text' => 'Bash', 'value' => 'bash'],
        ],
        'toolbar' => 'formatselect | bold italic strikethrough forecolor backcolor | link image media table | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat',
        'min_height'            => 200,
        'max_height'            => 700,
        'browser_spellcheck'    => true,
        'contextmenu'           => false,
        'menubar'               => false,
        'statusbar'             => false,
        'relative_urls'         => false,
        'convert_urls'          => false,
        'paste_data_images'     => true,
        'image_caption'         => false,
        'toolbar_drawer'        => false,
        'spoiler_caption'       => LANG_TINYMCE_SP,
        'toc_header'            => 'div',
        'resize'                => 'both',
        'theme'                 => 'silver',
        'mobile'                => [
            'theme' => 'silver'
        ],
        'smiles_url'            => false,
        'paste_as_text'         => false,
        'file_picker_types'     => 'file media',
        'file_upload'           => [],
        'allow_mime_types'      => [],
        'skin'                  => 'oxide',
        'images_preset'         => 'big'
    ];

    public function __construct($config = []) {

        $this->options = array_replace_recursive($this->options, $config);

        if(!$this->options['plugins']){
            $this->options['plugins'] = [];
        }

        $user = cmsUser::getInstance();
        $core = cmsCore::getInstance();
        $lang = cmsCore::getLanguageName();

        if($lang !== 'en'){
            $this->options['language'] = $lang;
        }

        // формируем плагины по кнопкам тулбаров
        $toolbar = explode(' ', preg_replace('#\s+#', ' ', $this->options['toolbar']));
        $quickbars_selection_toolbar = !empty($this->options['quickbars_selection_toolbar']) ? explode(' ', preg_replace('#\s+#', ' ', $this->options['quickbars_selection_toolbar'])) : [];
        $quickbars_insert_toolbar = !empty($this->options['quickbars_insert_toolbar']) ? explode(' ', preg_replace('#\s+#', ' ', $this->options['quickbars_insert_toolbar'])) : [];

        foreach ($this->buttons_mapping as $pname => $buttons) {
            if(is_numeric($pname)){
                $pname = $buttons;
                $buttons = [$pname];
            }
            if(in_array($pname, $this->options['plugins'])){
                continue;
            }
            foreach ($buttons as $button) {
                if(in_array($button, $toolbar)){
                    $this->options['plugins'][] = $pname;
                }
                if(in_array($button, $quickbars_selection_toolbar)){
                    $this->options['plugins'][] = $pname;
                }
                if(in_array($button, $quickbars_insert_toolbar)){
                    $this->options['plugins'][] = $pname;
                }
            }
        }

        $this->options['plugins'] = array_unique($this->options['plugins']);

        if ($user->is_logged) {

            $context = $core->getUriData();

            $upload_params = ['csrf_token' => cmsForm::getCSRFToken()];

            if($context['controller']){
                $upload_params['target_controller'] = $context['controller'];
            }

            if($context['action']){
                $upload_params['target_subject'] = mb_substr($context['action'], 0, 32);
            }

            if(strpos($core->uri, '/add/') === false && !empty($context['params'][1]) && is_numeric($context['params'][1])){
                $upload_params['target_id'] = $context['params'][1];
            }

            $upload_params_string = '?'.http_build_query($upload_params);

            $this->options['images_upload_url'] = href_to('images', 'upload_with_preset', ['file', $this->options['images_preset']]).$upload_params_string;

            $this->options['image_list'] = href_to('files', 'files_list', ['image']).$upload_params_string;

            if(!empty($this->options['allow_mime_types'])){

                $allowed_mime_types = [];

                foreach ($user->groups as $group_id) {
                    if(!empty($this->options['allow_mime_types'][$group_id])){
                        foreach ($this->options['allow_mime_types'][$group_id] as $mime_type) {
                            if(!in_array($mime_type, $allowed_mime_types)){
                                $allowed_mime_types[] = $mime_type;
                            }
                        }
                    }
                }

                if($allowed_mime_types){

                    unset($upload_params['csrf_token']);

                    cmsUser::sessionSet('ww_allowed_mime_types'.($upload_params ? ':'.implode(':', $upload_params) : ''), $allowed_mime_types);

                    $this->options['file_upload'] = [
                        'url' => href_to('files', 'upload_with_wysiwyg', ['inline_upload_file']).$upload_params_string
                    ];

                }

            }

        }

        $this->options['smiles_url'] = href_to('typograph', 'get_smiles');

        $this->options['plugins'] = implode(' ', $this->options['plugins']);

        if(!empty($this->options['block_formats'])){

            $block_formats = [];

            foreach ($this->options['block_formats'] as $tag) {
                $block_formats[] = $this->block_formats[$tag].'='.$tag.';';
            }

            $this->options['block_formats'] = implode(' ', $block_formats);

        } else {
            unset($this->options['block_formats']);
        }

        foreach ($this->options as $key => $value) {
            if(!$value){
                $this->options[$key] = false;
                continue;
            }
            if($value === 1){
                $this->options[$key] = true;
                continue;
            }
            if(is_numeric($value)){
                $this->options[$key] = intval($value);
                continue;
            }
        }

    }

	public function displayEditor($field_name, $content = '', $config = []){

        $this->loadRedactor();

        $dom_id = isset($this->options['id']) ? $this->options['id'] : 'wysiwyg-' . uniqid(); unset($this->options['id']);

        if($dom_id){
            if(!empty($this->options['wysiwyg_toolbar'])){
                echo '<div data-field_id="'.$dom_id.'" id="wysiwyg_toolbar_'.$dom_id.'" class="wysiwyg_toolbar_wrap">'.$this->options['wysiwyg_toolbar'].'</div>';
                unset($this->options['wysiwyg_toolbar']);
            }
            echo html_textarea($field_name, $content, ['id' => $dom_id, 'class' => 'tinymce_redactor']);
        }

        ob_start(); ?>

        <script>
            <?php if($dom_id){ ?>
                tiny_global_options['field_<?php echo $dom_id; ?>'] = <?php echo json_encode($this->options); ?>;
                $(function(){
                    init_tinymce('<?php echo $dom_id; ?>');
                });
            <?php } else { ?>
                tiny_global_options['default'] = <?php echo json_encode($this->options); ?>;
            <?php } ?>
        </script>

        <?php cmsTemplate::getInstance()->addBottom(ob_get_clean());
	}

    private function loadRedactor() {

        if(self::$redactor_loaded){ return false; }

        $template = cmsTemplate::getInstance();

        $template->addJSFromContext('wysiwyg/tinymce/files/tinymce.min.js');
        $template->addTplJSNameFromContext('files');

        $template_css = $template->getTemplateStylesFileName('theme');
        if($template_css){
            $template_css = $template->getHeadFilePath($template_css);
        }

        ob_start(); ?>

        <script>
            var tiny_global_options = {};
            function init_tinymce (dom_id){
                var tinymce_options = {};
                if(tiny_global_options.hasOwnProperty('field_'+dom_id)){
                    tinymce_options = tiny_global_options['field_'+dom_id];
                } else if(tiny_global_options.hasOwnProperty('default')) {
                    tinymce_options = tiny_global_options.default;
                }
                icms.files.url_delete = '<?php echo href_to('files', 'delete'); ?>';
                tinymce_options.selector = '#'+dom_id;
                tinymce_options.init_instance_callback = function (editor) {
                    editor.on('KeyDown', function (e) {
                        if ((e.keyCode === 8 || e.keyCode === 46) && editor.selection) {
                            var selectedNode = editor.selection.getNode();
                            if (selectedNode && selectedNode.nodeName === 'IMG' && !$(selectedNode).hasClass('smile_image') && confirm('<?php echo LANG_PARSER_IMAGE_DELETE; ?>')) {
                                icms.files.deleteByPath($(selectedNode).data('mce-src'));
                            }
                        }
                    });
                };
                tinymce_options.setup = function (editor) {
                    editor.addShortcut(
                    'ctrl+13', 'ctr + enter submit', function () {
                        $('#'+dom_id).trigger('keydown', [{keyCode: 13, ctrlKey: true}]);
                    });
                };
                <?php if($template_css){ ?>
                    tinymce_options.content_css = '<?php echo $template_css; ?>';
                <?php } ?>
                tinymce.init(tinymce_options);
                icms.forms.addWysiwygsInsertPool(dom_id, function(field_element, text){
                    tinymce.activeEditor.setContent(text);
                    tinymce.activeEditor.focus();
                });
                icms.forms.addWysiwygsAddPool(dom_id, function(field_element, text){
                    tinymce.activeEditor.insertContent(text);
                });
                icms.forms.addWysiwygsInitPool(dom_id, function(field_element){
                    tinymce.remove('#'+field_element);
                    init_tinymce(field_element);
                });
                icms.forms.addWysiwygsSavePool(dom_id, function(field_element){
                    tinymce.activeEditor.save();
                });
            }
        </script>

        <?php $template->addBottom(ob_get_clean());

        self::$redactor_loaded = true;
    }

    public function getParams() {

        $plugins = cmsCore::getDirsList('wysiwyg/tinymce/files/plugins', true);

        $hidden_plugins = ['code'];

        $buttons = $this->buttons;

        $buttons_mapping = $this->buttons_mapping;

        // убираем плагины, которые подключатся автоматически при назначении кнопок
        foreach ($buttons_mapping as $plugin_name => $pbtns) {
            if(is_numeric($plugin_name)){
                $plugin_name = $pbtns;
                $pbtns = [$plugin_name];
            }
            $key = array_search($plugin_name, $plugins);
            if($key !== false){
                unset($plugins[$key]);
            }
            foreach ($pbtns as $pbtn) {
                $buttons[] = $pbtn;
            }
        }

        // убираем скрытые плагины, которые включаются в самом классе редактора
        foreach ($hidden_plugins as $hidden_plugin) {
            $key = array_search($hidden_plugin, $plugins);
            if($key !== false){
                unset($plugins[$key]);
            }
        }

        return cmsEventsManager::hook('wysiwyg_tinymce_form_options', [$plugins, $buttons, $this->quickbars_insert_buttons, $this->quickbars_selection_buttons, $this->block_formats]);
    }

}
