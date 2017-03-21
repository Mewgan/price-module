<?php

namespace Jet\Modules\Price\Services;


use Doctrine\Common\Persistence\ObjectManager;
use Jet\Models\Website;
use Jet\Modules\Price\Models\Service;
use Jet\Modules\Price\Models\ServiceCategory;

trait LoadServiceFixture
{

    /**
     * @param ObjectManager $manager
     */
    public function loadService(ObjectManager $manager)
    {
        foreach ($this->data as $key => $data) {
            $website = ($this->hasReference($data['website'])) ? $this->getReference($data['website']) : Website::findOneByDomain($data['website']);
            $service = (Service::where('title', $data['title'])->where('website', $website)->count() == 0)
                ? new Service()
                : Service::findOneBy(['title' => $data['title'], 'website' => $website]);
            $service->setTitle($data['title']);
            $service->setPosition($data['position']);
            $service->setPrice($data['price']);
            $service->setDescription($data['description']);
            $service->setWebsite($website);
            if(isset($data['category'])) {
                $category = ($this->hasReference($data['category']))
                    ? $this->getReference($data['category'])
                    : ServiceCategory::findOneBy(['slug' => $data['category'], 'website' => $website]);
                if(!is_null($category)) $service->setCategory($category);
            }
            $this->setReference($key, $service);
            $manager->persist($service);
        }

        $manager->flush();
    }

}