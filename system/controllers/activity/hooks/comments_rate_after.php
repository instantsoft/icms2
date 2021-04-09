<?php

class onActivityCommentsRateAfter extends cmsAction {

    public function run($data) {

        list($comment, $score) = $data;

        $this->addEntry('comments', 'vote.comment', [
            'is_private'    => intval($comment['is_private']),
            'subject_title' => $comment['target_title'],
            'subject_id'    => $comment['id'],
            'subject_url'   => $comment['target_url'] . '#comment_' . $comment['id']
        ]);

        return [$comment, $score];
    }

}
