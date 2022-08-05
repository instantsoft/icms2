<?php

class onTagsCtypeListsContext extends cmsAction {

    private $lists = [
        'template' => [
            'search' => LANG_TAGS_CONTEXT_LT_SEARCH
        ]
    ];

    public function run($context = null) {

        if ($context === null) {
            return $this->lists;
        }

        $contexts = explode(':', $context);

        $context      = $contexts[0];
        $context_subj = isset($contexts[1]) ? $contexts[1] : false;

        if (!empty($this->lists[$context])) {
            return $this->lists[$context];
        }

        return false;
    }

}
