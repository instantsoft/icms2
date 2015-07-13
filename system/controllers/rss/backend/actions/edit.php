<?php

class actionRssEdit extends cmsAction {

    public function run($feed_id){

        if (!$feed_id) { cmsCore::error404(); }

        $rss_model = cmsCore::getModel('rss');

        $feed = $rss_model->getFeed($feed_id);

        $ctype_id = $feed['ctype_id'];

        $content_model = cmsCore::getModel('content');

        $fields = $content_model->getContentFields($feed['ctype_name']);

        $fields = array(''=>'') + array_collection_to_list($fields, 'name', 'title');

        $form = $this->getForm('feed', array($fields));

        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            $feed = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this,  $feed);

            if (!$errors){

                $rss_model->updateFeed($feed_id, $feed);

                $ctype = $content_model->getContentType($ctype_id);

                $ctype['options']['is_rss'] = $feed['is_enabled'];

                $content_model->updateContentType($ctype_id, array(
                    'options' => $ctype['options']
                ));

                $this->redirectToAction();

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('backend/edit', array(
            'feed' => $feed,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}

