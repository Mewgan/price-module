<?php
namespace Jet\Modules\Price\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Jet\Services\LoadFixture;

class LoadPriceModuleCategory extends AbstractFixture
{
    use LoadFixture;

    protected $data = [
        'name' => 'Price',
        'title' => 'Tarif',
        'slug' => 'price',
        'nav' => true,
        'description' => 'Module de tarifs',
        'icon' => 'fa fa-eur',
        'author' => 'S.Sumugan',
        'version' => '0.1',
        'update_available' => false,
        'access_level' => 4
    ];

    public function load(ObjectManager $manager)
    {
        $this->loadModuleCategory($manager);
    }
}