<?php
namespace Jet\Modules\Price\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Jet\Services\LoadFixture;

class LoadPriceModule extends AbstractFixture implements DependentFixtureInterface
{
    use LoadFixture;

    protected $data = [
        'module_price' => [
            'name' => 'Tarif',
            'slug' => 'price',
            'callback' => 'Jet\Modules\Price\Controllers\FrontServiceController@show',
            'description' => 'Affiche les tarifs',
            'category' => 'price',
            'access_level' => 4
        ],
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadModule($manager);
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    function getDependencies()
    {
        return [
            'Jet\Modules\Price\Fixtures\LoadPriceModuleCategory'
        ];
    }
}