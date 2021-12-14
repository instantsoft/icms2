<?php
class cmsWysiwygMarkitup {

    private static $redactor_loaded = false;

    private $options = [
        'skin' => 'simple',
        'set' => []
    ];

    public $default_set = [
        'resizeHandle' => false,
        'onShiftEnter' => [
            'keepDefault' => false,
            'replaceWith' => "<br />\n",
        ],
        'onCtrlEnter'  => ['keepDefault' => true],
        'onTab'        => [
            'keepDefault' => false,
            'replaceWith' => '    ',
        ],
        'markupSet' => [
            [
                'name'      => LANG_MARKITUP_B,
                'key'       => 'B',
                'openWith'  => '<b>',
                'closeWith' => '</b>',
                'className' => 'btnBold',
            ],
            [
                'name'      => LANG_MARKITUP_I,
                'key'       => 'I',
                'openWith'  => '<i>',
                'closeWith' => '</i>',
                'className' => 'btnItalic',
            ],
            [
                'name'      => LANG_MARKITUP_U,
                'key'       => 'U',
                'openWith'  => '<u>',
                'closeWith' => '</u>',
                'className' => 'btnUnderline',
            ],
            [
                'name'      => LANG_MARKITUP_S,
                'key'       => 'S',
                'openWith'  => '<s>',
                'closeWith' => '</s>',
                'className' => 'btnStroke',
            ],
            [
                'name'           => LANG_MARKITUP_UL,
                'openWith'       => '    <li>',
                'closeWith'      => '</li>',
                'multiline'      => true,
                'openBlockWith'  => "<ul>\n",
                'closeBlockWith' => "\n</ul>",
                'className'      => 'btnOl',
            ],
            [
                'name'           => LANG_MARKITUP_OL,
                'openWith'       => '    <li>',
                'closeWith'      => '</li>',
                'multiline'      => true,
                'openBlockWith'  => "<ol>\n",
                'closeBlockWith' => "\n</ol>",
                'className'      => 'btnUl',
            ],
            [
                'name'      => LANG_MARKITUP_BC,
                'openWith'  => '<blockquote>[!['.LANG_MARKITUP_BC_HINT.']!]',
                'closeWith' => '</blockquote>',
                'className' => 'btnQuote',
            ],
            [
                'name'        => LANG_MARKITUP_L,
                'key'         => 'L',
                'openWith'    => '<a target="_blank" href="[!['.LANG_MARKITUP_L1.':!:http://]!]">',
                'closeWith'   => '</a>',
                'placeHolder' => LANG_MARKITUP_L2,
                'className'   => 'btnLink',
            ],
            [
                'name'        => LANG_MARKITUP_IMGL,
                'replaceWith' => '<img src="[!['.LANG_MARKITUP_IMGL1.':!:http://]!]" alt="[!['.LANG_DESCRIPTION.']!]" />',
                'className'   => 'btnImg',
            ],
            [
                'name'      => LANG_MARKITUP_IMG,
                'className' => 'btnImgUpload',
                'beforeInsert' => true
            ],
            [
                'name'      => LANG_MARKITUP_YT,
                'openWith'  => '<youtube>[!['.LANG_MARKITUP_YT1.']!]',
                'closeWith' => '</youtube>',
                'className' => 'btnVideoYoutube',
            ],
            [
                'name'      => LANG_MARKITUP_FB,
                'openWith'  => '<facebook>[!['.LANG_MARKITUP_FB1.']!]',
                'closeWith' => '</facebook>',
                'className' => 'btnVideoFacebook',
            ],
            [
                'name'        => LANG_MARKITUP_CODE,
                'openWith'    => '<code type="[!['.LANG_MARKITUP_CODE1.':!:php]!]">',
                'placeHolder' => "\n\n",
                'closeWith'   => '</code>',
                'className'   => 'btnCode',
            ],
            [
                'name'        => LANG_MARKITUP_SP,
                'openWith'    => '<spoiler title="[!['.LANG_MARKITUP_SP1.':!:'.LANG_MARKITUP_SP.']!]">',
                'placeHolder' => "\n\n",
                'closeWith'   => '</spoiler>',
                'className'   => 'btnSpoiler',
            ],
            [
                'name'      => LANG_MARKITUP_SM,
                'className' => 'btnSmiles',
                'key'       => 'Z',
                'beforeInsert' => true
            ]
        ]
    ];

    public function __construct($config = []) {

        $this->options['set'] = $this->default_set;
        $this->options['set']['data'] = [
            'smiles_url'   => href_to('typograph', 'get_smiles'),
            'upload_title' => LANG_UPLOAD,
            'upload_url'   => href_to('images', 'upload_with_preset', ['inline_upload_file', 'wysiwyg_markitup'])
        ];

        if(isset($config['id'])){
            $this->options['id'] = $config['id'];
        }

        if(!empty($config['set'])){
            $this->options['set'] = array_replace_recursive($this->options['set'], $config['set']);
        }

        if(!empty($config['buttons'])){
            foreach ($this->options['set']['markupSet'] as $btn_id => $btn) {
                if(!in_array($btn_id, $config['buttons'])){
                    unset($this->options['set']['markupSet'][$btn_id]);
                }
            }
        }

    }

    public function displayEditor($field_name, $content = '', $config = []) {

        $this->loadRedactor();

        $dom_id = isset($this->options['id']) ? $this->options['id'] : 'wysiwyg-' . uniqid();

        if($dom_id){
            if(!empty($this->options['wysiwyg_toolbar'])){
                echo '<div data-field_id="'.$dom_id.'" id="wysiwyg_toolbar_'.$dom_id.'" class="wysiwyg_toolbar_wrap">'.$this->options['wysiwyg_toolbar'].'</div>';
                unset($this->options['wysiwyg_toolbar']);
            }
            echo html_textarea($field_name, $content, [
                'id' => $dom_id,
                'class' => 'markitup_redactor'
            ]);
        }

        ob_start(); ?>

        <script>
            <?php if($dom_id){ ?>
                markitup_global_options['field_<?php echo $dom_id; ?>'] = <?php echo json_encode($this->options['set']); ?>;
                $(function(){
                    init_markitup('<?php echo $dom_id; ?>');
                });
            <?php } else { ?>
                markitup_global_options['default'] = <?php echo json_encode($this->options['set']); ?>;
            <?php } ?>
        </script>

        <?php cmsTemplate::getInstance()->addBottom(ob_get_clean());
    }

    private function loadRedactor() {

        if(self::$redactor_loaded){ return false; }

        $template = cmsTemplate::getInstance();

        $template->addJSFromContext('wysiwyg/markitup/image_upload.js');
        $template->addJSFromContext('wysiwyg/markitup/insert_smiles.js');
        $template->addJSFromContext('wysiwyg/markitup/jquery.markitup.js');

        $css_file = 'wysiwyg/markitup/skins/'.$this->options['skin'].'/style.css';
        $tpl_css_file = $template->getTplFilePath('css/wysiwyg/markitup/styles.css', false);
        if($tpl_css_file){
            $css_file = $tpl_css_file;
        }

        $template->addCSSFromContext($css_file);

        ob_start(); ?>

        <script>
            var markitup_global_options = {};
            function init_markitup (dom_id){
                var mconfig = {};
                if(markitup_global_options.hasOwnProperty('field_'+dom_id)){
                    mconfig = markitup_global_options['field_'+dom_id];
                } else if(markitup_global_options.hasOwnProperty('default')) {
                    mconfig = markitup_global_options.default;
                }
                if(9 in mconfig.markupSet && mconfig.markupSet[9].beforeInsert === true){
                    mconfig.markupSet[9].beforeInsert = function(markItUp) { InlineUpload.display(markItUp); };
                }
                if(mconfig.markupSet[14] && mconfig.markupSet[14].beforeInsert === true){
                    mconfig.markupSet[14].beforeInsert = function(markItUp) { insertSmiles.displayPanel(markItUp); };
                }
                $('#'+dom_id).markItUp(mconfig);
            }
        </script>

        <?php $template->addBottom(ob_get_clean());

        self::$redactor_loaded = true;
    }

}
