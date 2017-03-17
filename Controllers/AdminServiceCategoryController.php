<?php

namespace Jet\Modules\Price\Controllers;

use Cocur\Slugify\Slugify;
use Jet\AdminBlock\Controllers\AdminController;
use Jet\Models\Website;
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
                $role = new ServiceCategory();
                $role->setName($name);
                $role->setSlug($slugify->slugify($name));
                $role->setPosition($position);
                $role->setWebsite(Website::findOneById($website));
                if (ServiceCategory::watchAndSave($role))
                    return ['status' => 'success', 'message' => 'La catégorie a bien été créée', 'resource' => $role];
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

                $role = new ServiceCategory();
                $replace = true;
            }

            $category->setName($name);
            $category->setPosition($position);
            $category->setSlug($slugify->slugify($name));
            $category->setWebsite($website);

            if (ServiceCategory::watchAndSave($category)) {
                if ($replace) {
                    $this->reassignService($old_category, $category, $website);
                    $website = $role->getWebsite();
                    $data = $this->replaceData($website->getData(), 'team_roles', $id, $role->getId());
                    $website->setData($data);
                    Website::watchAndSave($website);
                }
                return ['status' => 'success', 'message' => 'Le rôle a bien été mis à jour'];
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
        $teams = $old_role->getTeams();
        /** @var Team $member */
        foreach ($teams as $member) {
            if (in_array($member->getWebsite()->getId(), $this->websites)) {
                /** @var Team $member */
                if ($member->getWebsite() != $website) {
                    /** @var Team $new_team */
                    $new_member = new Team;
                    $new_member->setFullName($member->getFullName());
                    $new_member->setPhoto($member->getPhoto());
                    $new_member->setDescription($member->getDescription());
                    $new_member->setGender($member->getGender());
                    $new_member->setOrder($member->getOrder());
                    $new_member->removeRole($old_role);
                    $new_member->addRole($role);
                    $new_member->setWebsite($website);

                    $data = $this->excludeData($data, 'teams', $member->getId());
                    $data = $this->replaceData($data, 'teams', $member->getId(), $new_member->getId());

                    Team::watch($new_member);
                } else {
                    $member->removeRole($old_role);
                    $member->addRole($role);
                    Team::watch($member);
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
                return ['status' => 'error', 'message' => 'Vous n\'avez pas les permissions pour supprimer ces rôles'];

            $data = $website->getData();

            $roles = TeamRole::repo()->findById($request->get('ids'));
            $ids = [];

            foreach ($roles as $role) {
                $data = $this->removeData($data, 'team_roles', $role['id']);
                if ($role['website']['id'] != $website->getId()) {
                    $data = $this->excludeData($data, 'team_roles', $role['id']);
                } else
                    $ids[] = $role['id'];
            }

            $website->setData($data);
            Website::watchAndSave($website);

            return (TeamRole::destroy($ids))
                ? ['status' => 'success', 'message' => 'Les rôles ont bien été supprimés']
                : ['status' => 'error', 'message' => 'Erreur lors de la suppression'];
        }
        return ['status' => 'error', 'message' => 'Les rôles n\'ont pas pu être supprimés'];
    }

}