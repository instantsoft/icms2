<?php
class actionComplaintsOrfo extends cmsAction {

    public function run(){
        
        if (!$this->request->isAjax()){ cmsCore::error404(); } 
       
        $orfo    = $this->request->get('orfo');
        $url     = $this->request->get('url');
        $comment = $this->request->get('comment', false);   
        $author  = !cmsUser::isLogged() ?  cmsUser::getIp() : cmsUser::get('nickname');        
                       
        $form = $this->getForm('orfo');
        $is_submitted = $this->request->has('submit'); 
        
    if ($is_submitted){
        
        $data = $form->parse($this->request, $is_submitted);
        $data['date'] = date('Y-m-d H:i:s');
        $errors = $form->validate($this, $data);
               dump($errors);
        if (!$errors){	
			
            $this->model->addComplaints($data);

            $messenger = cmsCore::getController('messages');
            $messenger->addRecipient(1);
    
            $notice = array(
                    'content' => sprintf(LANG_COMPLAINTS_ADD_NOTICE, $url, $orfo),
                    'options' => array(
                    'is_closeable' => true
                    ),
            );

	    $messenger->ignoreNotifyOptions()->sendNoticePM($notice, 'complaints_add');
  
	}

    cmsTemplate::getInstance()->renderJSON( array( 
        'errors' => false, 
        'callback' => 'formSuccess'
        ));    	
     
   
    }
    $data = array('orfo'    => $orfo,
                  'url'     => $url,
                  'author'  => $author,
                  'comment' => $comment);  
    
    return cmsTemplate::getInstance()->render('orfo', array(
            'form'=>$form,
            'data'=>$data
         ));   
   }
   
}