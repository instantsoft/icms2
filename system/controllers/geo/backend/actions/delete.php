<?php

class actionGeoDelete extends cmsAction {

    public function run($type = null, $id = null){

		if(!$type || !$id){ cmsCore::error404(); }

		switch($type){

			case 'city':

				$this->model->delete('geo_cities', $id);

                $this->cms_cache->clean('geo.cities');

				break;

			case 'region':

				$this->model->filterEqual('region_id', $id);
				$this->model->deleteFiltered('geo_cities');
				$this->model->delete('geo_regions', $id);

                $this->cms_cache->clean('geo.cities');
                $this->cms_cache->clean('geo.regions');

				break;

			case 'country':

				$this->model->filterEqual('country_id', $id);
				$this->model->deleteFiltered('geo_cities');

				$this->model->filterEqual('country_id', $id);
				$this->model->deleteFiltered('geo_regions');

				$this->model->delete('geo_countries', $id);

                $this->cms_cache->clean('geo.cities');
                $this->cms_cache->clean('geo.regions');
                $this->cms_cache->clean('geo.countries');

				break;

            default:

                cmsCore::error404();

		}

        cmsUser::addSessionMessage(string_lang('LANG_GEO_SUCCESS_DELETE_'.$type), 'success');

        $this->redirectBack();

    }

}
