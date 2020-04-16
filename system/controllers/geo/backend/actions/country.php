<?php

class actionGeoCountry extends cmsAction {

    public function run($country_id = null){

        $country = array(
            'ordering' => $this->model->getNextOrdering('geo_countries')
        );

        if($country_id){
			$country = $this->model->getItemById('geo_countries', $country_id);
		}

        $form = $this->getForm('country');

        if($this->request->has('submit')){

            $country = $form->parse($this->request, true);
            $errors  = $form->validate($this,  $country);

            if(!$errors){

				if($country_id){

					$this->model->update('geo_countries', $country_id, $country);

					cmsUser::addSessionMessage(LANG_GEO_COUNTRY_UPDATED, 'success');

				} else {

					$this->model->insert('geo_countries', $country);

					cmsUser::addSessionMessage(LANG_GEO_COUNTRY_ADDED, 'success');

				}

                $this->cms_cache->clean('geo.cities');
                $this->cms_cache->clean('geo.regions');
                $this->cms_cache->clean('geo.countries');

				$this->redirectToAction('');

			}

			cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

        }

        $this->cms_template->setPageH1((isset($country['name']) ? $country['name'] : LANG_GEO_ADD_COUNTRY));

        return $this->cms_template->render('backend/country', array(
            'do'      => $country_id ? 'edit' : 'add',
            'country' => $country,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ));

    }

}
