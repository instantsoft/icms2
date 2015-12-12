<?php

class actionTagsEdit extends cmsAction {

    public function run($tag_id){

        if (!$tag_id) { cmsCore::error404(); }

        $form = $this->getForm('tag');

        $is_submitted = $this->request->has('submit');

        $tag = $this->model->getTag($tag_id);

        $original_tag = $tag['tag'];

        if ($is_submitted){

            $tag = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this,  $tag);

            if ($original_tag == $tag['tag']) { $this->redirectToAction(); }

            if (!$errors){

                $duplicate_id = $this->model->getTagId($tag['tag']);

                if (!$duplicate_id){
                    $this->model->updateTag($tag_id, $tag);
                }

                if ($duplicate_id){
                    $this->model->mergeTags($tag_id, $duplicate_id);
                    cmsUser::addSessionMessage(sprintf(LANG_TAGS_MERGED, $original_tag, $tag['tag']), 'success');
                }

                $this->redirectToAction();

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('backend/tag', array(
            'do' => 'edit',
            'tag' => $tag,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}