<?php

namespace Appstore\Bundle\RestaurantBundle\Repository;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Doctrine\ORM\EntityRepository;



/**
 * ProductionElementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductionBatchRepository extends EntityRepository
{

    protected function handleSearchBetween($qb,$data)
    {

        $name = isset($data['name'])? $data['name'] :'';
        $startDate = isset($data['startDate'])? $data['startDate'] :'';
        $endDate = isset($data['endDate'])? $data['endDate'] :'';

        if (!empty($startDate) ) {
            $start = date('Y-m-d 00:00:00',strtotime($data['startDate']));
            $qb->andWhere("e.created >= :startDate")->setParameter('startDate', $start);
        }
        if (!empty($endDate)) {
            $end = date('Y-m-d 23:59:59',strtotime($data['endDate']));
            $qb->andWhere("e.created <= :endDate")->setParameter('endDate',$end);
        }
        if(!empty($name)){
            $qb->andWhere("e.slug = :name")->setParameter('name', $name);
        }


    }

    public function handleDateRangeFind($qb,$data)
    {

        if(empty($data['startDate']) and empty($data['endDate'])){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }

        if (!empty($data['startDate']) ) {
            $qb->andWhere("e.created >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }

        if (!empty($data['endDate'])) {
            $qb->andWhere("e.created <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }
    }

    public function findWithSearch(RestaurantConfig $config,$data)
    {
        $config = $config->getId();

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $config) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.created','DESC');
        $qb->getQuery();
        return  $qb;
    }

    public function productionBatchItemUpdate(Particular  $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.issueQuantity) AS quantity');
        $qb->where('e.productionItem = :particular')->setParameter('particular', $stockItem->getId());
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }


}
