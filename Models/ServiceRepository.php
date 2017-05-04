<?php

namespace Jet\Modules\Price\Models;

use Doctrine\ORM\QueryBuilder;
use Jet\Models\AppRepository;

/**
 * Class ServiceRepository
 * @package Jet\Modules\Price\Models
 */
class ServiceRepository extends AppRepository
{

    /**
     * @param array $params
     * @return array
     */
    public function listAll($params = [])
    {
        $query = Service::queryBuilder();

        $query->select('s')
            ->addSelect('partial c.{id,name,slug,updated_at}')
            ->from('Jet\Modules\Price\Models\Service', 's')
            ->leftJoin('s.category', 'c')
            ->leftJoin('s.website', 'w')
            ->addOrderBy('c.position', 'ASC')
            ->addOrderBy('s.position', 'ASC');

        $query = $this->getQueryWithParams($query, $params);

        return $this->reassignCategories($query->getQuery()->getArrayResult(), $params);
    }

    /**
     * @param $ids
     * @return array
     */
    public function findById($ids)
    {
        $query = Service::queryBuilder()
            ->select('partial s.{id}')
            ->addSelect('partial w.{id}')
            ->from('Jet\Modules\Price\Models\Service', 's')
            ->leftJoin('s.website', 'w');
        return $query->where($query->expr()->in('s.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()->getArrayResult();
    }

    /**
     * @param array $data
     * @param array $params
     * @return array
     */
    private function reassignCategories($data = [], $params = [])
    {
        $categories = ServiceCategory::repo()->listAll(['websites' => $params['websites']]);
        $exclude_ids = isset($params['options']['parent_exclude']['service_categories']) ? array_flip($params['options']['parent_exclude']['service_categories']) : [];
        $in_cat = null;
        if (isset($params['categories']) && is_array($params['categories']) && !empty($params['categories'])) {
            foreach ($params['categories'] as $k => $cat) {
                if (isset($exclude_ids[$cat])) {
                    unset($params['categories'][$k]);
                }
                if (isset($params['options']['parent_replace']['service_categories'][$cat])) {
                    $params['categories'][$k] = $params['options']['parent_replace']['service_categories'][$cat];
                }
            }
            $in_cat = array_flip($params['categories']);
        }
        foreach ($data as $i => $service) {
            if (isset($service['category']['id'])) {
                if (isset($exclude_ids[$service['category']['id']])) {
                    unset($data[$i]);
                }
                if (isset($params['options']['parent_replace']['service_categories'][$service['category']['id']])) {
                    $index = findIndex($categories, 'id', $params['options']['parent_replace']['service_categories'][$service['category']['id']]);
                    if ($index !== false) {
                        $data[$i]['category'] = $categories[$index];
                    }
                }
                if (!is_null($in_cat) && isset($data[$i]['category']['id']) && !isset($in_cat[$data[$i]['category']['id']])) {
                    unset($data[$i]);
                }
            }
        }
        return $data;
    }

    /**
     * @param QueryBuilder $query
     * @param $params
     * @return QueryBuilder
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
            $query = $this->excludeData($query, $params['options'], 'services');
        }

        return $query;
    }
} 