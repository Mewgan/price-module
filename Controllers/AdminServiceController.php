<?php

namespace Jet\Modules\Price\Controllers;

use Jet\AdminBlock\Controllers\AdminController;
use Jet\Models\Website;
use Jet\Modules\Price\Models\Service;
use Jet\Modules\Price\Models\ServiceCategory;
use Jet\Modules\Price\Requests\ServiceRequest;
use Jet\Services\Auth;

/**
 * Class AdminServiceController
 * @package Jet\Modules\Price\Controllers
 */
class AdminServiceController extends AdminController
{

    /**
     * @param $website
     * @return array
     */
    public function all($website)
    {
        if ($this->getWebsite($website)) {
            $params = [
                'websites' => $this->websites,
                'options' => $this->getWebsiteData($this->website)
            ];

            return ['status' => 'success', 'resource' => Service::repo()->listAll($params)];
        }
        return ['status' => 'error', 'Site non trouvé'];
    }


    /**
     * @param ServiceRequest $request
     * @param Auth $auth
     * @param $website
     * @return array
     */
    public function updateOrCreate(ServiceRequest $request, Auth $auth, $website)
    {
        if ($request->method() == 'PUT') {
            if ($request->has('services')) {

                $services = $request->get('services');

                if (!$this->isWebsiteOwner($auth, $website))
                    return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour modifier ces contenus'];

                /** @var Website $website */
                $website = Website::findOneById($website);
                if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site'];

                foreach ($services as $value) {

                    $service = (isset($value['id']) && !empty($value['id']) && substr($value['id'], 0, 6) != "create")
                        ? Service::findOneById($value['id'])
                        : new Service();

                    if(is_null($service)) return ['status' => 'error', 'message' => 'Impossible de trouver le service'];

                    /** @var Website $service_website */
                    $service_website = $service->getWebsite();

                    if ($service_website != $website && !is_null($service_website)) {
                        $data = $this->excludeData($website->getData(), 'services', $service->getId());
                        $website->setData($data);
                        Website::watch($website);
                        $service = new Service();
                    }

                    $response = $this->updateFields($request, $service, $website, $value);
                    if (is_array($response)) return $response;

                }

                if (Service::save()) {
                    $response = $this->all($website->getId());
                    return ($response['status'] == 'success')
                        ? ['status' => 'success', 'message' => 'Les services ont bien été mis à jour', 'resource' => $response['resource']]
                        : $response;
                }
                return ['status' => 'error', 'message' => 'Les service n\'ont pas pu être mis à jour'];
            }
            return ['status' => 'error', 'message' => 'Aucune donnée envoyée'];
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }


    /**
     * @param ServiceRequest $request
     * @param Service $service
     * @param Website $website
     * @param $value
     * @return array|bool
     */
    private function updateFields(ServiceRequest $request, Service $service, Website $website, $value)
    {
        $response = $request->validate($request->rules(), $request::$messages, $value);
        if ($response === true) {

            $service->setTitle($value['title']);
            $service->setPrice($value['price']);
            $service->setDescription($value['description']);
            $service->setPosition($value['position']);
            $service->setWebsite($website);

            $category = ServiceCategory::findOneById($value['category']['id']);
            if(is_null($category))
                return ['status' => 'error', 'message' => 'Impossible de trouver la catégorie pour le service : ' . $value['title']];
            $service->setCategory($category);

            Service::watch($service);

            return true;
        }
        return $response;
    }


    /**
     * @param ServiceRequest $request
     * @param Auth $auth
     * @param $website
     * @return array
     */
    public function delete(ServiceRequest $request, Auth $auth, $website)
    {
        if ($request->method() == 'DELETE' && $request->exists('ids')) {
            /** @var Website $website */
            $website = Website::findOneById($website);
            if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];
            $data = $website->getData();

            if (!$this->isWebsiteOwner($auth, $website->getId()))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour supprimer ces services'];

            $services = Service::repo()->findById($request->get('ids'));
            $ids = [];

            foreach ($services as $service) {
                if ($service['website']['id'] != $website->getId()) {
                    $data = $this->excludeData($data, 'services', $service['id']);
                } else
                    $ids[] = $service['id'];
            }

            $website->setData($data);
            Website::watchAndSave($website);

            return (Service::destroy($ids))
                ? ['status' => 'success', 'message' => 'Le service a bien été supprimé']
                : ['status' => 'error', 'message' => 'Erreur lors de la suppression'];
        }
        return ['status' => 'error', 'message' => 'Le service n\'a pas pu être supprimé'];
    }

}