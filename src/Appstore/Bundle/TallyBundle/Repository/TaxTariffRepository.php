<?php

namespace Appstore\Bundle\TallyBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\TallyBundle\Entity\TaxTariff;
use Doctrine\ORM\EntityRepository;

/**
 * ItemBrandRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TaxTariffRepository extends EntityRepository
{
    protected function handleSearchBetween($qb,$data)
    {
        if(!empty($data))
        {
            $name = isset($data['name'])  ? $data['name'] : '';
            if (!empty($name)) {
                $qb->andWhere("e.name LIKE :name")->setParameter('name', $name.'%');
            }
        }

    }

    public function findWidthSearch($data = array()){

        $sort = isset($data['sort'])? $data['sort'] :'e.hsCode';
        $direction = isset($data['direction'])? $data['direction'] :'ASC';
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.name IS NOT NULL');
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy("{$sort}",$direction);
        $result =  $qb->getQuery();
        return $result;
    }

    public function totalTax(TaxTariff $tariff){

        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.customsDuty + e.supplementaryDuty + e.valueAddedTax + e.advanceIncomeTax + e.recurringDeposit) as total');
        $qb->where("e.id={$tariff->getId()}");
        $result =  $qb->getQuery()->getOneOrNullResult()['total'];
        return $result;
    }


    public function searchAutoComplete($q)
    {

        $query = $this->createQueryBuilder('e');
        $query->select("CONCAT(e.hsCode,'-', e.name) as id");
        $query->addSelect("CONCAT(e.hsCode,'-', e.name) as text");
        $query->where($query->expr()->like("e.name", "'%$q%'"  ));
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

}