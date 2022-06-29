<?php

class modelRss extends cmsModel {

    private function feed() {
        return $this->getItem('rss_feeds', function ($item, $model) {
            $item['mapping'] = cmsModel::yamlToArray($item['mapping']);
            $item['image']   = cmsModel::yamlToArray($item['image']);
            return $item;
        });
    }

    public function getFeed($id) {
        return $this->filterEqual('id', $id)->feed();
    }

    public function getFeedByCtypeId($ctype_id) {
        return $this->filterEqual('ctype_id', $ctype_id)->feed();
    }

    public function getFeedByCtypeName($ctype_name) {
        return $this->filterEqual('ctype_name', $ctype_name)->feed();
    }

    public function updateFeed($id, $feed) {
        return $this->update('rss_feeds', $id, $feed);
    }

    public function addFeed($feed) {
        return $this->insert('rss_feeds', $feed);
    }

    public function deleteFeed($id) {
        return $this->delete('rss_feeds', $id);
    }

    public function isCtypeFeed($ctype_name) {
        return $this->filterEqual('name', $ctype_name)->getFieldFiltered('content_types', 'id');
    }

}
