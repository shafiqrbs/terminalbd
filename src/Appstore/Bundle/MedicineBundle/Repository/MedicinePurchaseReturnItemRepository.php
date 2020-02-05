<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Entity\MedicineVendor;
use Doctrine\ORM\EntityRepository;


/**
 * HmsVendorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedicinePurchaseReturnItemRepository extends EntityRepository
{
    public function purchaseReturnStockItemUpdate(MedicinePurchaseItem $item)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.medicinePurchaseItem = :purchaseItem')->setParameter('purchaseItem', $item->getId());
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function purchaseReturnStockUpdate(MedicineStock $item)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.medicineStock = :medicineStock')->setParameter('medicineStock', $item->getId());
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }


    public function checkInInsert(MedicineConfig $config , $vendor)
    {
        $entity = $this->findOneBy(array('medicineConfig' => $config,'companyName' => $vendor));
        if(empty($entity)){
            $entity = new MedicineVendor();
            $entity->setMedicineConfig($config);
            $entity->setCompanyName($vendor);
            $entity->setName($vendor);
            $this->_em->persist($entity);
            $this->_em->flush();
        }
        return $entity;
    }

}
