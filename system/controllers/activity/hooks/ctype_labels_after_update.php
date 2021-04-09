<?php

class onActivityCtypeLabelsAfterUpdate extends cmsAction {

    public function run($ctype) {

        if ($this->isTypeExists('content', "add.{$ctype['name']}")) {

            $this->updateType('content', "add.{$ctype['name']}", [
                'title'       => sprintf(LANG_CONTENT_ACTIVITY_ADD, $ctype['labels']['many_genitive']),
                'description' => sprintf(LANG_CONTENT_ACTIVITY_ADD_DESC, $ctype['labels']['one_accusative'], '%s')
            ]);

        } else {

            $this->addType([
                'controller'  => 'content',
                'name'        => "add.{$ctype['name']}",
                'is_enabled'  => 0,
                'title'       => sprintf(LANG_CONTENT_ACTIVITY_ADD, $ctype['labels']['many_genitive']),
                'description' => sprintf(LANG_CONTENT_ACTIVITY_ADD_DESC, $ctype['labels']['one_accusative'], '%s')
            ]);
        }

        return $ctype;
    }

}
