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

        $query = $this->getQueryWithParams($query, $params);

        $query->orderBy('c.position', 'ASC');

        return (isset($params['reassign']) && $params['reassign'] == false)
            ? $query->getQuery()->getArrayResult()
            : $this->reassignServices($query->getQuery()->getArrayResult(), $params);
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
     * @param array $data
     * @param array $params
     * @return array
     */
    private function reassignServices($data = [], $params = [])
    {
        if (isset($params['websites']) && isset($params['service_in_category']) && $params['service_in_category'] == true) {
            $categories = ServiceCategory::repo()->listAll(['websites' => $params['websites'], 'service_in_category' => true, 'options' => [], 'reassign' => false]);
            $replace_ids = isset($params['options']['parent_replace']['service_categories']) ? array_flip($params['options']['parent_replace']['service_categories']) : [];
            foreach ($data as $i => $category) {
                if (isset($replace_ids[$category['id']])) {
                    $index = findIndex($categories, 'id', $replace_ids[$category['id']]);
                    if ($index !== false) {
                        $data[$i]['services'] = $categories[$index]['services'];
                    }
                }
            }
        }
        return $data;
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

        if (isset($params['options'])) {
            $query = $this->excludeData($query, $params['options'], 'service_categories', 'c');
        }

        if (isset($params['service_in_category']) && $params['service_in_category'] == true) {
            $query->addSelect('s')
                ->leftJoin('c.services', 's')
                ->addOrderBy('s.position', 'ASC');

            $query = $this->excludeData($query, $params['options'], 'services', 's');
        }

        return $query;
    }

} 