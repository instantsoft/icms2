<?php

    $this->setPageTitle($tab['title'], $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($profile['nickname'], href_to_profile($profile));
    $this->addBreadcrumb($tab['title']);

    echo $html;
