<?php

    $this->addBreadcrumb(LANG_USERS_CFG_TABS);

    $this->renderGrid($this->href_to('tabs_ajax'), $grid);
