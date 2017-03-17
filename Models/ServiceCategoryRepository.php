<?php

namespace Jet\Modules\Price\Models;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ServiceCategoryRepository
 * @package Jet\Modules\Price\Models
 */
class ServiceCategoryRepository extends EntityRepository
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

        $query = $this->getQueryWithParams($query, $params);

        $query->orderBy('c.id', 'DESC');

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
            ->leftJoin('t.website', 'w');
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

        if (isset($params['options']) && isset($params['options']['parent_exclude']) && isset($params['options']['parent_exclude']['service_categories']) && !empty($params['options']['parent_exclude']['service_categories'])) {
            $query->andWhere($query->expr()->notIn('c.id', ':exclude_ids'))
                ->setParameter('exclude_ids', $params['options']['parent_exclude']['service_categories']);
        }

        return $query;
    }

} 