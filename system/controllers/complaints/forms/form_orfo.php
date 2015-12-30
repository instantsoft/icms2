<?php
class formComplaintsOrfo extends cmsForm {
    
    public function init() {

        return array(
            
            array( 'type' => 'fieldset', 
                   'title' => LANG_COMPLAINTS_ORFO_TITLE, 
                   'childs' => array( 

                    new fieldHtml('comment', array(
                        'rules' => array(
                            array('max_length', 300)
                            ),
                        'options'=>array('size' => 5)
                        )
                    ),
                    
                    new fieldHidden('url', array(
                        'title' => LANG_COMPLAINTS_ORFO_TITLE,
                        'rules' => array(
                             array('required'))
                        )
                    ),
                
                    new fieldHidden('author', array(
                        'title' => LANG_COMPLAINTS_ORFO_TITLE,                       
                        )
                    ),    
                    new fieldHidden('orfo', array(
                        'title' => LANG_COMPLAINTS_ORFO_ERROR, 
                        'rules' => array(
                            array('required'), 
                            array('max_length', 300)
                            ))
                    )
            ),
        ));
    }
}