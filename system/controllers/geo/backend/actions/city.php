<?php

class actionGeoCity extends cmsAction {

    public function run($city_id = null, $region_id = null){

        $city    = array();
        $region  = array();
        $country = array();

        if($city_id){

			$city = $this->model->getCityParents($city_id);
            if(!$city){ cmsCore::error404(); }

            $country = array(
                'id'   => $city['country_id'],
                'name' => $city['country_name']
            );

            $region = array(
                'id'   => $city['region_id'],
                'name' => $city['region_name']
            );

		} else {

            if(!$region_id){ cmsCore::error404(); }

            $region = $this->model->getRegionParents($region_id);
            if(!$region){ cmsCore::error404(); }

            $country = array(
                'id'   => $region['country_id'],
                'name' => $region['country_name']
            );

            $city = array(
                'country_id' => $country['id'],
                'region_id'  => $region['id']
            );

        }

        $form = $this->getForm('city', array($country['id']));

        if($this->request->has('submit')){

            $city = $form->parse($this->request, true);

            $errors = $form->validate($this,  $city);

            if(!$errors){

				if($city_id){

					$this->model->update('geo_cities', $city_id, $city);

                    $this->cms_cache->clean('geo.city');

					cmsUser::addSessionMessage(LANG_GEO_CITY_UPDATED, 'success');

				} else {

					$this->model->insert('geo_cities', $city);

					cmsUser::addSessionMessage(LANG_GEO_CITY_ADDED, 'success');

				}

                $this->cms_cache->clean('geo.cities');

				$this->redirectToAction('cities', array($region['id'], $country['id']));

			}

            cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

        }

        $this->cms_template->setPageH1(array($country['name'], $region['name'], (isset($city['name']) ? $city['name'] : LANG_GEO_ADD_CITY)));

        return $this->cms_template->render('backend/city', array(
            'do'      => $city_id ? 'edit' : 'add',
            'city'    => $city,
            'region'  => $region,
            'country' => $country,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ));

    }

}
