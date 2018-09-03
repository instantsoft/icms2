<?php
class widgetTagsCloud extends cmsWidget {

    public function run(){

        return cmsCore::getController('tags')->getTagsWidgetParams($this->getOptions());

    }

}
