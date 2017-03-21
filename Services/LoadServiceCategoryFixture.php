<?php

namespace Jet\Modules\Price\Services;


use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Website;
use Jet\Modules\Price\Models\ServiceCategory;

trait LoadServiceCategoryFixture
{

    /**
     * @param ObjectManager $manager
     */
    public function loadServiceCategory(ObjectManager $manager)
    {
        foreach ($this->data as $key => $data) {
            $website = null;
            if(isset($data['website']) && !is_null($data['website']))
                $website = ($this->hasReference($data['website'])) ? $this->getReference($data['website']) : Website::findOneByDomain($data['website']);

            $category = (ServiceCategory::where('slug', $data['slug'])->where('website', $website)->count() == 0)
                ? new ServiceCategory()
                : ServiceCategory::findOneBy(['slug' => $data['slug'], 'website' => $website]);
            $category->setName($data['name']);
            $category->setSlug($data['slug']);
            $category->setPosition($data['position']);
            if(!is_null($website))
                $category->setWebsite($website);
            $this->setReference($key, $category);
            $manager->persist($category);
        }

        $manager->flush();
    }

    /**
     * @param $data
     * @param Website $website
     * @return mixed
     */
    public function getServiceCategoryContent($data, Website $website)
    {
        if (isset($data['data']) && isset($data['data']['categories']) && is_array($data['data']['categories'])) {
            $nex_categories = [];
            foreach ($data['data']['categories'] as $category){
                $category = ($this->hasReference($category))
                    ? $this->getReference($category) : ServiceCategory::findOneBy(['slug' => $category, 'website' => $website]);
                $nex_categories[] = $category->getId();
            }
            $data['data']['categories'] = $nex_categories;
        }
        return $data;
    }

}