<?php

namespace Jet\Modules\Price\Controllers;

use Cocur\Slugify\Slugify;
use Jet\AdminBlock\Controllers\AdminController;
use Jet\Models\Website;
use Jet\Modules\Price\Models\Service;
use Jet\Modules\Price\Models\ServiceCategory;
use Jet\Services\Auth;
use JetFire\Http\Request;

/**
 * Class AdminServiceCategoryController
 * @package Jet\Modules\Price\Controllers
 */
class AdminServiceCategoryController extends AdminController
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

            return ['status' => 'success', 'resource' => ServiceCategory::repo()->listAll($params)];
        }
        return ['status' => 'error', 'Site non trouvé'];
    }

    /**
     * @param Request $request
     * @param Auth $auth
     * @param Slugify $slugify
     * @param $website
     * @return array
     */
    public function create(Request $request, Auth $auth, Slugify $slugify, $website)
    {
        if ($request->method() == 'POST') {

            if (!$this->isWebsiteOwner($auth, $website))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour créer une catégorie'];

            if(!$request->has('name'))
                return ['status' => 'error', 'message' => 'Aucune donnée reçue'];

            if(!$request->has('position'))
                return ['status' => 'error', 'message' => 'Veuillez définir l\'order d\'affichage de la catégorie'];

            $name = $request->get('name');
            $position = $request->get('position');

            if (ServiceCategory::where('name', $name)->where('website', $website)->count() == 0) {
                $service = new ServiceCategory();
                $service->setName($name);
                $service->setSlug($slugify->slugify($name));
                $service->setPosition($position);
                $service->setWebsite(Website::findOneById($website));
                if (ServiceCategory::watchAndSave($service))
                    return ['status' => 'success', 'message' => 'La catégorie a bien été créée', 'resource' => $service];
                return ['status' => 'error', 'message' => 'La catégorie n\'a pas été créée'];
            }
            return ['status' => 'error', 'message' => 'La catégorie existe déjà'];
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }

    /**
     * @param Request $request
     * @param Auth $auth
     * @param Slugify $slugify
     * @param $id
     * @param $website
     * @return array
     */
    public function update(Request $request, Auth $auth, Slugify $slugify, $id, $website)
    {
        if ($request->method() == 'PUT') {

            if (!$this->isWebsiteOwner($auth, $website))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour mettre à jour un rôle'];

            $name = $request->get('name');
            $position = $request->get('position');
            $replace = false;

            /** @var Website $website */
            $website = Website::findOneById($website);
            if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];

            if (ServiceCategory::where('id', '!=', $id)->where('name', $name)->where('website', $website)->count() > 0)
                return ['status' => 'error', 'message' => 'La catégorie existe déjà'];

            /** @var ServiceCategory $category */
            $category = ServiceCategory::findOneById($id);
            if (is_null($category)) return ['status' => 'error', 'message' => 'Impossible de trouver la catégorie'];

            $old_category = $category;

            if (is_null($category->getWebsite()) || $category->getWebsite()->getId() != $website->getId()) {
                $data = $this->excludeData($website->getData(), 'service_categories', $category->getId());
                $website->setData($data);
                Website::watch($website);

                $category = new ServiceCategory();
                $replace = true;
            }

            $category->setName($name);
            $category->setPosition($position);
            $category->setSlug($slugify->slugify($name));
            $category->setWebsite($website);

            if (ServiceCategory::watchAndSave($category)) {
                if ($replace) {
                    $this->reassignService($old_category, $category, $website);
                    $website = $category->getWebsite();
                    $data = $this->replaceData($website->getData(), 'service_categories', $id, $category->getId());
                    $website->setData($data);
                    Website::watchAndSave($website);
                }
                return ['status' => 'success', 'message' => 'La catégorie a bien été mis à jour'];
            } else
                return ['status' => 'error', 'message' => 'Erreur lors de la mise à jour'];
        }
        return ['status' => 'error', 'message' => 'Requête non autorisée'];
    }

    /**
     * @param ServiceCategory $old_category
     * @param ServiceCategory $category
     * @param Website $website
     */
    private function reassignService(ServiceCategory $old_category, ServiceCategory $category, Website $website)
    {
        $data = $website->getData();
        $this->getWebsite($website);
        $services = $old_category->getServices();
        /** @var Service $service */
        foreach ($services as $service) {
            if (in_array($service->getWebsite()->getId(), $this->websites)) {
                /** @var Service $service */
                if ($service->getWebsite() != $website) {
                    /** @var Service $new_service */
                    $new_service = new Service();
                    $new_service->setTitle($service->getTitle());
                    $new_service->setDescription($service->getDescription());
                    $new_service->setPosition($service->getPosition());
                    $new_service->setPrice($service->getPrice());
                    $new_service->setCategory($category);
                    $new_service->setWebsite($website);

                    $data = $this->excludeData($data, 'services', $service->getId());
                    $data = $this->replaceData($data, 'services', $service->getId(), $new_service->getId());

                    Service::watch($new_service);
                } else {
                    $service->setCategory($category);
                    Service::watch($service);
                }
            }
        }
        $website->setData($data);
        Website::watch($website);
    }

    /**
     * @param Request $request
     * @param Auth $auth
     * @param $website
     * @return array
     */
    public function delete(Request $request, Auth $auth, $website)
    {
        if ($request->method() == 'DELETE' && $request->exists('ids')) {

            /** @var Website $website */
            $website = Website::findOneById($website);
            if (is_null($website)) return ['status' => 'error', 'message' => 'Impossible de trouver le site web'];

            if (!$this->isWebsiteOwner($auth, $website->getId()))
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour supprimer ces catégories'];

            $data = $website->getData();

            $categories = ServiceCategory::repo()->findById($request->get('ids'));
            $ids = [];

            foreach ($categories as $category) {
                $data = $this->removeData($data, 'service_categories', $category['id']);
                if ($category['website']['id'] != $website->getId()) {
                    $data = $this->excludeData($data, 'service_categories', $category['id']);
                } else
                    $ids[] = $category['id'];
            }

            $website->setData($data);
            Website::watchAndSave($website);

            return (ServiceCategory::destroy($ids))
                ? ['status' => 'success', 'message' => 'Les catégories ont bien été supprimées']
                : ['status' => 'error', 'message' => 'Erreur lors de la suppression'];
        }
        return ['status' => 'error', 'message' => 'Les catégories n\'ont pas pu être supprimées'];
    }

}