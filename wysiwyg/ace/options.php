<?php
class formWysiwygAceOptions extends cmsForm {

    public function init($do) {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_WW_OPTIONS,
                'childs' => array(

                    new fieldList('options:theme', array(
                        'title' => LANG_ACE_THEME,
                        'generator' => function($item){
                            $items = [];
                            $ps = cmsCore::getFilesList('wysiwyg/ace/files/', 'theme-*', true);
                            foreach($ps as $p){
                                $name = str_replace('theme-', '', $p);
                                $items['ace/theme/'.$name] = ucfirst(str_replace('_', ' ', $name));
                            }
                            return $items;
                        },
                        'default' => 'ace/theme/dreamweaver'
                    )),

                    new fieldNumber('options:fontSize', array(
                        'title' => LANG_ACE_FONTSIZE,
                        'units' => 'px',
                        'default' => 12
                    )),

                    new fieldCheckbox('options:enableSnippets', array(
                        'title' => LANG_ACE_ENABLESNIPPETS,
                        'default' => true
                    )),

                    new fieldCheckbox('options:enableEmmet', array(
                        'title' => LANG_ACE_ENABLEEMMET,
                        'default' => true
                    )),

                    new fieldCheckbox('options:enableBasicAutocompletion', array(
                        'title' => LANG_ACE_ENABLEBASICAUTOCOMPLETION,
                        'default' => true
                    )),

                    new fieldCheckbox('options:enableLiveAutocompletion', array(
                        'title' => LANG_ACE_ENABLELIVEAUTOCOMPLETION,
                        'default' => true
                    )),

                    new fieldCheckbox('options:wrap', array(
                        'title' => LANG_ACE_WRAP,
                        'default' => true
                    )),

                    new fieldCheckbox('options:showInvisibles', array(
                        'title' => LANG_ACE_SHOWINVISIBLES,
                        'default' => false
                    )),

                    new fieldCheckbox('options:showGutter', array(
                        'title' => LANG_ACE_SHOWGUTTER,
                        'default' => true
                    )),

                    new fieldCheckbox('options:showLineNumbers', array(
                        'title' => LANG_ACE_SHOWLINENUMBERS,
                        'default' => true
                    )),

                    new fieldCheckbox('options:displayIndentGuides', array(
                        'title' => LANG_ACE_DISPLAYINDENTGUIDES,
                        'default' => true
                    ))

                )
            )

        );

    }

}
