<?php

namespace Appstore\Bundle\EcommerceBundle\Repository;
use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Appstore\Bundle\EcommerceBundle\Entity\ItemBrand;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Doctrine\ORM\EntityRepository;

/**
 * ItemBrandRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemBrandRepository extends EntityRepository
{
    public function getLastId(EcommerceConfig $config)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(cs)');
        $qb->from('configBundle:ItemBrand','cs');
        $qb->where("cs.ecommerceConfig = :config");
        $qb->setParameter('config', $config);
        $count = $qb->getQuery()->getSingleScalarResult();
        if($count > 0 ){
            return $count+1;
        }else{
            return 1;
        }

    }

    public function searchAutoComplete($q, EcommerceConfig $config)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.ecommerceConfig', 'ic');
        $query->select('e.name as id');
        $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->andWhere("ic.id = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.id');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }

    public function insertBrand(MedicineStock $stock)
    {
        $config = $stock->getMedicineConfig()->getGlobalOption()->getEcommerceConfig();
        $entity = $this->findOneBy(array('ecommerceConfig' => $config,'name' => $stock->getBrandName()));
        if(empty($entity)){

            $brand = new ItemBrand();
            $brand->setEcommerceConfig($config);
            $brand->setName($stock->getBrandName());
            $this->_em->persist($brand);
            $this->_em->flush();
            return $brand;
        }
        return $entity;
    }
}
