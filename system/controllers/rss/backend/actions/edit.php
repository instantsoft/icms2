<?php

class actionRssEdit extends cmsAction {

    public function run($feed_id){

        $feed = $this->model->getFeed($feed_id);
        if (!$feed) { cmsCore::error404(); }

        $form = $this->getForm('feed');

        // выясняем контроллер ленты
        $controller = $feed['ctype_name'];
        if($this->model->isCtypeFeed($feed['ctype_name'])){
            $controller = 'content';
        }

        list($form, $feed) = cmsEventsManager::hook('rss_'.$controller.'_controller_form', array($form, $feed));
        list($form, $feed) = cmsEventsManager::hook('rss_edit_form', array($form, $feed));
        list($form, $feed) = cmsEventsManager::hook('rss_'.$feed['ctype_name'].'_edit_form', array($form, $feed));

        if ($this->request->has('submit')){

            $feed = array_merge($feed, $form->parse($this->request, true));
            $errors = $form->validate($this,  $feed);

            if (!$errors){

                $this->model->updateFeed($feed_id, $feed);

                cmsEventsManager::hook('rss_'.$controller.'_controller_after_update', $feed);

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