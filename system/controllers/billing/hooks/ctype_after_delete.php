<?php
/**
 * @property \modelBilling $model
 */
class onBillingCtypeAfterDelete extends cmsAction {

    public function run($ctype) {

        $this->model->
                filterEqual('controller', 'content')->
                filterEqual('name', "{$ctype['name']}_add")->
                deleteActions();

        $this->model->
                filterEqual('ctype_id', $ctype['id'])->
                deletePaidFields();

        $this->model->
                filterEqual('ctype_id', $ctype['id'])->
                deleteVipFields();

        $this->model->
                filterEqual('ctype_id', $ctype['id'])->
                deleteTerms();

        return $ctype;
    }

}
