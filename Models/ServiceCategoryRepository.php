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
            ->leftJoin('c.website', 'w')
            ->addOrderBy('c.position', 'ASC');

        $query = $this->getQueryWithParams($query, $params);

        return $this->reassignServices($query->getQuery()->getArrayResult(), $params);
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
        if (isset($params['websites']) && isset($params['service_in_category']) && $params['service_in_category'] === true) {
            $categories = $data;
            $params['exclude_categories'] = isset($params['exclude_categories']) ? array_flip($params['exclude_categories']) : [];
            $replace_ids = isset($params['options']['parent_replace']['service_categories']) ? array_flip($params['options']['parent_replace']['service_categories']) : [];
            $exclude_ids = isset($params['options']['parent_exclude']['services']) ? array_flip($params['options']['parent_exclude']['services']) : [];

            $in_cat = null;
            if (isset($params['categories']) && is_array($params['categories']) && !empty($params['categories'])) {
                foreach ($params['categories'] as $k => $cat) {
                    if (isset($params['exclude_categories'][$cat])) {
                        unset($params['categories'][$k]);
                    }
                    if (isset($params['options']['parent_replace']['service_categories'][$cat])) {
                        $params['categories'][$k] = $params['options']['parent_replace']['service_categories'][$cat];
                    }
                }
                $in_cat = array_flip($params['categories']);
            }

            foreach ($data as $i => $category) {
                if (isset($params['exclude_categories'][$category['id']])) {
                    unset($data[$i]);
                }
                if (isset($replace_ids[$category['id']])) {
                    $data[$i] = $category;
                    if (count($category['services']) <= 0) {
                        $index = findIndex($categories, 'id', $replace_ids[$category['id']]);
                        if ($index !== false) {
                            $data[$i]['services'] = $categories[$index]['services'];
                        }
                    }
                }
                if (!is_null($in_cat) && isset($data[$i]['id']) && !isset($in_cat[$data[$i]['id']])) {
                    unset($data[$i]);
                }
                foreach ($category['services'] as $y => $service) {
                    if (isset($exclude_ids[$service['id']])) unset($data[$i]['services'][$y]);
                }
            }
        }
        ksort($data);
        return $data;
    }

    /**
     * @param QueryBuilder $query
     * @param $params
     * @return mixed
     */
    private function getQueryWithParams(QueryBuilder $query, &$params)
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

        if (isset($params['service_in_category']) && $params['service_in_category'] == true) {
            $query->addSelect('s')
                ->leftJoin('c.services', 's')
                ->addOrderBy('s.position', 'ASC');

            if (isset($params['websites'])) {
                $query->leftJoin('s.website', 'sw')
                    ->andWhere($query->expr()->in('sw.id', ':websites'));
            }

            if (isset($params['options']['parent_exclude']['service_categories'])) {
                $params['exclude_categories'] = $params['options']['parent_exclude']['service_categories'];
                unset($params['options']['parent_exclude']['service_categories']);
            }
        }

        if (isset($params['options'])) {
            $query = $this->excludeData($query, $params['options'], 'service_categories', 'c');
        }

        return $query;
    }

} 