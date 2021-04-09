<?php

class onActivityCommentsAfterDeleteList extends cmsAction {

    public function run($comments_ids) {

        $this->deleteEntry('comments', 'vote.comment', $comments_ids);

        return $comments_ids;
    }

}
