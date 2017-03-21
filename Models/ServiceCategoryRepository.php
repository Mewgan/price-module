<?php

namespace Jet\Modules\Price\Models;

use Doctrine\ORM\QueryBuilder;
use Jet\Models\AppRepository;

/**
 * Class ServiceCategoryRepository
 * @package Jet\Modules\Price\Models
 */
class ServiceCategoryRepository extends AppRepository
{


    /**
     * @param array $params
     * @return array
     */
    public function listAll($params = [])
    {
        $query = ServiceCategory::queryBuilder();

        $query->select('c')
            ->from('Jet\Modules\Price\Models\ServiceCategory', 'c')
            ->leftJoin('c.website', 'w');

        if(isset($params['service_in_category']) && $params['service_in_category'] == true){
            $query->addSelect('s')
                ->leftJoin('c.services', 's')
                ->orderBy('s.position', 'ASC');
        }

        $query = $this->getQueryWithParams($query, $params);

        $query->addOrderBy('c.position', 'ASC');

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param $ids
     * @return array
     */
    public function findById($ids)
    {
        $query = ServiceCategory::queryBuilder()
            ->select('partial c.{id}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Price\Models\ServiceCategory', 'c')
            ->leftJoin('c.website', 'w');
        return $query->where($query->expr()->in('c.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()->getArrayResult();
    }

    /**
     * @param QueryBuilder $query
     * @param $params
     * @return mixed
     */
    private function getQueryWithParams(QueryBuilder $query, $params)
    {

        if (isset($params['websites'])) {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->in('w.id', ':websites'),
                    $query->expr()->isNull('w.id')
                )
            )->setParameter('websites', $params['websites']);
        } else {
            $query->andWhere($query->expr()->isNull('w.id'));
        }

        if (isset($params['options'])){
            $query = $this->excludeData($query, $params['options'], 'service_categories', 'c');
        }
        
        return $query;
    }

} 