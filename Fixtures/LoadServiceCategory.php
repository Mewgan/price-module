<?php
namespace Jet\Modules\Price\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Modules\Price\Services\LoadServiceCategoryFixture;

class LoadServiceCategory extends AbstractFixture
{

    use LoadServiceCategoryFixture;
    
    protected $data = [
        'woman-service-category' => [
            'name' => 'Femme',
            'slug' => 'femme',
            'position' => 0,
        ],
        'man-service-category' => [
            'name' => 'Homme',
            'slug' => 'homme',
            'position' => 1,
        ],
        'other-service-category' => [
            'name' => 'Autres',
            'slug' => 'autres',
            'position' => 2,
        ],
    ];

    public function load(ObjectManager $manager)
    {
       $this->loadServiceCategory($manager);
    }

}