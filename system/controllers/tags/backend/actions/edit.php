<?php
/**
 * @property \modelTags $model
 */
class actionTagsEdit extends cmsAction {

    public function run($tag_id) {

        $form = $this->getForm('tag');

        $tag = $this->model->getTag($tag_id);
        if (!$tag) {
            return cmsCore::error404();
        }

        $original_tag = $tag;

        if ($this->request->has('submit')) {

            $tag    = $form->parse($this->request, true);
            $errors = $form->validate($this, $tag);

            if (!$errors) {

                if ($original_tag['tag'] == $tag['tag']) {

                    $this->model->updateTag($tag_id, $tag);

                    cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                    return $this->redirectToAction();
                }

                $duplicate_id = $this->model->getTagId($tag['tag']);

                if (!$duplicate_id) {

                    $this->model->updateTag($tag_id, $tag);

                    $this->model->replaceTargetTags($tag_id, $tag['tag'], $original_tag['tag']);

                    cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');
                }

                if ($duplicate_id) {

                    $this->model->mergeTags($tag_id, $duplicate_id);

                    cmsUser::addSessionMessage(sprintf(LANG_TAGS_MERGED, $original_tag['tag'], $tag['tag']), 'success');
                }

                return $this->redirectToAction();
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/tag', [
            'do'     => 'edit',
            'tag'    => $tag,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
